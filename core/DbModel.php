<?php

namespace core;

/**
 * NexaPHP Framework Core - Database Model (Active Record)
 * ---
 * WARNING: DO NOT MODIFY THIS FILE.
 * Provides abstracted database interactions for all application models.
 * ---
 */
abstract class DbModel extends Model
{
    public int $id = 0;
    public ?string $uuid = '';
    public ?string $created_at = '';
    abstract public static function tableName(): string;

    abstract public function attributes(): array;

    abstract public static function primaryKey(): string;

    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $primaryKey = static::primaryKey();

        if (empty($this->{$primaryKey})) {
            // INSERT
            if (in_array('uuid', $attributes) && empty($this->uuid)) {
                $this->uuid = $this->generateUuid();
            }

            $columns = array_map(fn($attr) => "`$attr`", $attributes);
            $params = array_map(fn($attr) => ":$attr", $attributes);
            $statement = self::prepare("INSERT INTO $tableName (" . implode(',', $columns) . ") 
                    VALUES (" . implode(',', $params) . ")");
            foreach ($attributes as $attribute) {
                $statement->bindValue(":$attribute", $this->{$attribute});
            }
        } else {
            // UPDATE
            $params = array_map(fn($attr) => "`$attr` = :$attr", $attributes);
            $sql = "UPDATE $tableName SET " . implode(',', $params) . " WHERE `$primaryKey` = :$primaryKey";

            $statement = self::prepare($sql);
            foreach ($attributes as $attribute) {
                $statement->bindValue(":$attribute", $this->{$attribute});
            }
            $statement->bindValue(":$primaryKey", $this->{$primaryKey});
        }

        $statement->execute();
        if (empty($this->{$primaryKey})) {
            $this->{$primaryKey} = Application::$app->db->pdo->lastInsertId();
        }
        return true;
    }

    public function delete()
    {
        $tableName = $this->tableName();
        $primaryKey = static::primaryKey();
        $sql = "DELETE FROM $tableName WHERE `$primaryKey` = :$primaryKey";

        $statement = self::prepare($sql);
        $statement->bindValue(":$primaryKey", $this->{$primaryKey});

        return $statement->execute();
    }

    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }

    public static function findByUuid(string $uuid)
    {
        return static::findOne(['uuid' => $uuid]);
    }

    public static function findOne($where, $orderBy = '')
    {
        $tableName = static::tableName();

        $attributes = array_keys($where);
        $sql = "SELECT * FROM $tableName WHERE " . implode(" AND ", array_map(fn($attr) => "`$attr` = :$attr", $attributes));

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        $statement = self::prepare("$sql LIMIT 1");
        foreach ($where as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        $statement->execute();
        return $statement->fetchObject(static::class);
    }

    public static function getAll($where = [])
    {
        $tableName = static::tableName();

        $sql = "SELECT * FROM $tableName";
        $conditions = [];
        $params = [];

        foreach ($where as $key => $value) {
            $conditions[] = "`$key` = :$key";
            $params[$key] = $value;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $statement = self::prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue(":$key", $value);
        }
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_CLASS, static::class);
    }

    public static function paginate($page = 1, $limit = 15, $where = [], $search = '', $searchFields = [])
    {
        $tableName = static::tableName();
        $offset = ($page - 1) * $limit;

        $conditions = [];
        $params = [];

        foreach ($where as $key => $value) {
            $conditions[] = "`$key` = :$key";
            $params[$key] = $value;
        }

        if (!empty($search) && !empty($searchFields)) {
            $searchConditions = [];
            foreach ($searchFields as $field) {
                $searchConditions[] = "`$field` LIKE :search";
            }
            $conditions[] = "(" . implode(" OR ", $searchConditions) . ")";
            $params['search'] = "%$search%";
        }

        $sql = "SELECT * FROM $tableName";
        $countSql = "SELECT COUNT(*) FROM $tableName";

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
            $countSql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY `id` DESC LIMIT $limit OFFSET $offset";

        $statement = self::prepare($sql);
        $countStatement = self::prepare($countSql);

        foreach ($params as $key => $value) {
            $statement->bindValue(":$key", $value);
            $countStatement->bindValue(":$key", $value);
        }

        $statement->execute();
        $countStatement->execute();

        $total = (int)$countStatement->fetchColumn();

        return [
            'items' => $statement->fetchAll(\PDO::FETCH_CLASS, static::class),
            'total' => $total,
            'page' => (int)$page,
            'limit' => (int)$limit,
            'pages' => ceil($total / $limit)
        ];
    }


    protected function generateUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
