<?php

require_once __DIR__ . '/Key.php';

const DB_FILE = 'vpn.db';
class DB
{
    protected SQLite3 $db;

    public function __construct()
    {
        $this->db = new SQLite3(DB_FILE, SQLITE3_OPEN_READWRITE);

        // Debug mode
//        $this->db->exec("DROP TABLE IF EXISTS client;");
//        $this->db->exec("DROP TABLE IF EXISTS config;");


        $this->db->exec("CREATE TABLE IF NOT EXISTS client (id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, IPv4 TEXT NOT NULL, key_private TEXT, key_public TEXT, is_granted INTEGER DEFAULT 0, handshake_at TEXT, active_at TEXT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS config (key TEXT NOT NULL PRIMARY KEY, value TEXT);");
    }

    public function begin(): void
    {
        $this->db->exec('BEGIN;');
    }

    public function commit(): void
    {
        $this->db->exec('COMMIT;');
    }

    public function client_insert(string $IPv4, ?string $key_private, ?string $key_public): void
    {
        $query = $this->db->prepare('INSERT INTO client (IPv4, key_private, key_public) VALUES (:IPv4, :key_private, :key_public)');
        $query->bindValue(':IPv4', $IPv4, SQLITE3_TEXT);
        $query->bindValue(':key_private', $key_private, SQLITE3_TEXT);
        $query->bindValue(':key_public', $key_public, SQLITE3_TEXT);
        $query->execute();
    }

    public function peer_append(string $key_private, string $key_public, bool $random = false) {
        $query = $this->db->prepare('UPDATE client SET key_private = :key_private, key_public = :key_public, is_granted = 1 WHERE is_granted = 0 ORDER BY id ASC LIMIT 1');
        $query->bindValue(':key_private', $key_private, SQLITE3_TEXT);
        $query->bindValue(':key_public', $key_public, SQLITE3_TEXT);
        $query->execute();

        return $this->db->lastInsertRowID();
    }

    public function config_insert(string $key, string $value)
    {
        $query = $this->db->prepare('INSERT INTO config (key, value) VALUES (:key, :value)');
        $query->bindValue(':key', $key, SQLITE3_TEXT);
        $query->bindValue(':value', $value, SQLITE3_TEXT);
        $query->execute();
    }

    public function fill_client_pool(
        string $IPv4_network,
        int    $IPv4_mask,
        bool   $fill_key_private = false,
        bool   $fill_key_public = false,
    )
    {
        $this->begin();

        $IPv4_counter = pow(2, 32 - $IPv4_mask);
        $IPv4_start = ip2long($IPv4_network);

        for ($i = 0; $i < $IPv4_counter; $i++) {
            if ($i == 0) {
                $this->config_insert('network_IPv4', long2ip($IPv4_start + $i));
                $this->config_insert('network_mask_IPv4', $IPv4_mask);
            } elseif ($i == 1) {
                $this->config_insert('server_IPv4', long2ip($IPv4_start + $i));
            } elseif (($i + 1) < $IPv4_counter) {
                $key_private = null;
                $key_public = null;

                if ($fill_key_private || $fill_key_public) {
                    $key = new Key();

                    if ($fill_key_private) {
                        $key_private = $key->private();
                    }

                    if ($fill_key_public) {
                        $key_public = $key->public($key_private);
                    }
                }

                $IPv4_client = long2ip($IPv4_start + $i);
                $this->client_insert($IPv4_client, $key_private, $key_public);
            } elseif ($i + 1 == $IPv4_counter) {
                $this->config_insert('broadcast_IPv4', long2ip($IPv4_start + $i));
            }
        }

        $this->commit();
    }
}