<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Patient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\Binary;
use Throwable;

use function base64_encode;
use function count;
use function sprintf;
use function substr;

/**
 * End-to-end Queryable Encryption demo: create the encrypted collection,
 * insert documents, rewrap the data keys from one local master key to another,
 * and read the documents back.
 */
class QeDemo extends Command
{
    protected $signature = 'qe:demo';

    protected $description = 'Insert encrypted documents, rewrap the data keys to a new local master key, and read them back.';

    private const KEY_VAULT_DATABASE = 'encryption';

    private const KEY_VAULT_COLLECTION = '__demoKeyVault';

    public function handle(): int
    {
        $this->createEncryptedCollection();
        $this->insertPatients();

        $before = $this->readPatients('after insert');

        $keysBefore = $this->dataKeys();
        $this->table(
            ['data key id', 'keyMaterial (first 16 chars)', 'provider wrapping it'],
            $this->keyRows($keysBefore),
        );

        $this->rewrap();

        $keysAfter = $this->dataKeys();
        $this->table(
            ['data key id', 'keyMaterial (first 16 chars)', 'provider wrapping it'],
            $this->keyRows($keysAfter),
        );

        $this->assertSameKeys($keysBefore, $keysAfter);

        $after = $this->readPatients('after rewrap');
        $this->assertReadable($before, $after);

        $this->showAtRest();

        $this->newLine();
        $this->info('Done. The data keys kept their id and key material, only the master key wrapping them changed.');

        return self::SUCCESS;
    }

    private function createEncryptedCollection(): void
    {
        $this->step('Create the encrypted "patients" collection');

        $connection = DB::connection('mongodb');

        try {
            $connection->getSchemaBuilder()->drop('patients');
        } catch (Throwable) {
            // Nothing to drop on the first run.
        }

        // A fresh vault too, so the demo is repeatable and never rewraps keys
        // that belong to something else.
        try {
            DB::connection('mongodb_plain')
                ->getDatabase(self::KEY_VAULT_DATABASE)
                ->dropCollection(self::KEY_VAULT_COLLECTION);
        } catch (Throwable) {
            // Nothing to drop on the first run.
        }

        $this->call('mongodb:encryption:create-collection', ['collection' => 'patients']);
    }

    private function insertPatients(): void
    {
        $this->step('Insert documents');

        Patient::create(['name' => 'Alice', 'ssn' => '123-45-6789']);
        Patient::create(['name' => 'Bob', 'ssn' => '987-65-4321']);
        Patient::create(['name' => 'Carol', 'ssn' => '111-22-3333']);

        $this->line(sprintf('  Inserted %d patients.', Patient::count()));
    }

    /** @return array<string, string> */
    private function readPatients(string $label): array
    {
        $this->step('Read documents ' . $label);

        $patients = [];

        foreach (Patient::query()->orderBy('name')->get() as $patient) {
            $patients[$patient->name] = $patient->ssn;
            $this->line(sprintf('  %-6s ssn = %s', $patient->name, $patient->ssn));
        }

        return $patients;
    }

    private function rewrap(): void
    {
        $this->step('Rewrap the data keys from "local" to "local:rotated"');

        $this->call('mongodb:encryption:rewrap-data-keys', ['--provider' => 'local:rotated', '--force' => true]);
    }

    private function showAtRest(): void
    {
        $this->step('What the server stores (read with a plain connection)');

        $plain = DB::connection('mongodb_plain')->getCollection('patients');

        foreach ($plain->find([], ['sort' => ['name' => 1]]) as $document) {
            $ssn = $document['ssn'];
            $this->line(sprintf(
                '  %-6s ssn = %s (%s)',
                $document['name'],
                $ssn instanceof Binary ? base64_encode($ssn->getData()) : $ssn,
                $ssn instanceof Binary ? 'encrypted' : 'plaintext',
            ));
        }
    }

    /** @return array<string, object> */
    private function dataKeys(): array
    {
        $vault = DB::connection('mongodb_plain')
            ->getDatabase(self::KEY_VAULT_DATABASE)
            ->selectCollection(self::KEY_VAULT_COLLECTION);

        $keys = [];

        foreach ($vault->find() as $key) {
            // A data key id is a UUID, stored as Binary. Read it as bytes, not
            // as an ObjectId.
            $keys[base64_encode($key->_id->getData())] = $key;
        }

        return $keys;
    }

    /** @param array<string, object> $keys */
    private function keyRows(array $keys): array
    {
        $rows = [];

        foreach ($keys as $id => $key) {
            $rows[] = [
                substr($id, 0, 16) . '...',
                substr(base64_encode($key->keyMaterial->getData()), 0, 16) . '...',
                $key->masterKey['provider'] ?? 'unknown',
            ];
        }

        return $rows;
    }

    /** @param array<string, object> $before */
    /** @param array<string, object> $after */
    private function assertSameKeys(array $before, array $after): void
    {
        if (count($before) !== count($after)) {
            $this->error('The number of data keys changed, which should not happen.');
        }

        foreach ($after as $id => $key) {
            if (! isset($before[$id])) {
                $this->error(sprintf('Data key %s appeared, which should not happen.', $id));
            }
        }
    }

    /** @param array<string, string> $before */
    /** @param array<string, string> $after */
    private function assertReadable(array $before, array $after): void
    {
        if ($before !== $after) {
            $this->error('The documents do not read the same before and after the rewrap.');

            return;
        }

        $this->info(sprintf('  All %d documents read identically before and after the rewrap.', count($after)));
    }

    private function step(string $title): void
    {
        $this->newLine();
        $this->line('==> ' . $title);
    }
}
