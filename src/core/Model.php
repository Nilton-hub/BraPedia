<?php

namespace src\core;

use PDOException;
use src\models\Model as ModelInterface;
use Willry\QueryBuilder\Connect;
use Willry\QueryBuilder\DB;

class Model
{
    /** @var ModelInterface $model */
    private ModelInterface $model;

    /** @var array  */
    private array $columns;

    /**
     * @param ModelInterface $model
     */
    public function __construct(ModelInterface $model)
    {
        $this->model = $model;
        $this->setColumns();
    }

    /**
     * @return string|null
     */
    public function create(): ?string
    {
        return DB::table($this->model::entity())
            ->create($this->columns);
    }

    /**
     * @param array|null $whereColumns
     * @param array $columns
     * @return DB
     */
    public function read(?array $whereColumns = null, array $columns = ["*"]): DB
    {
        if (!$whereColumns) {
            return DB::table($this->model::entity())->select($columns);
        }
        $fields = [];
        foreach ($whereColumns as $column) {
            $fields[] = "{$column} = :{$column}";
            if (method_exists($this->model, $column)) {
                $where[$column] = $this->model->$column();
            }
        }
        $fields = implode(" AND ", $fields);
        return DB::table($this->model::entity())
            ->select($columns)
            ->where($fields, $where);
    }

    /**
     * @param string|null $whereTerms
     * @param array|null $whereParams
     * @param array $data
     * @return int|null
     */
    public function update(?string $whereTerms, ?array $whereParams, array $data): ?int
    {
        if (!$whereTerms) {
            return DB::table($this->model::entity())
                ->update($data);
        }
        return DB::table($this->model::entity())
            ->where($whereTerms, $whereParams)
            ->update($data);
    }

    /**
     * @param string|null $whereTerms
     * @param array|null $whereParams
     * @return int|null
     */
    public function delete(?string $whereTerms, ?array $whereParams): ?int
    {
        if (!$whereTerms){
            return DB::table($this->model::entity())
                ->delete();
        }
        return DB::table($this->model::entity())
            ->where($whereTerms, $whereParams)
            ->delete();
    }

    /**
     * @return PDOException|null
     */
    private function setColumns(): ?PDOException
    {
        try {
            $stmt = Connect::getInstance()->prepare("DESC {$this->model::entity()}");
            $stmt->execute();
            $columns = [];
            foreach ($stmt->fetchAll() as $item) {
                $column = $item->Field;
                if (method_exists($this->model, $column)) {
                    $columns[$column] = $this->model->$column();
                }
            }
            foreach ($columns as $i => $column) {
                if (in_array($i, $this->model::safe())) {
                    unset($columns[$i]);
                }
            }
            $this->columns = $columns;
            return null;
        } catch (PDOException $exception) {
            return $exception;
        }
    }

    /**
     * @return ModelInterface
     */
    public function model(): ModelInterface
    {
        return $this->model;
    }
}
