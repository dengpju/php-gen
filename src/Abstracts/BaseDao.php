<?php
declare(strict_types=1);


namespace Dengpju\PhpGen\Abstracts;

use Hyperf\DbConnection\Model\Model;

/**
 * @property Model $model
 * Class BaseDao
 * @package Dengpju\PhpGen\Dao
 */
abstract class BaseDao
{
    /**
     * @var string
     */
    protected string $primaryKey;
    /**
     * @var int
     */
    protected int $page = 1;
    /**
     * @var int
     */
    protected int $perPage = 20;

    /**
     * BaseDao constructor.
     */
    public function __construct()
    {
        $this->primaryKey = $this->model->getKeyName();
        try {
            $this->page = (int)input('page', $this->page);
            $this->perPage = (int)input('page_size', $this->perPage);
        } catch (\Throwable $exception) {

        }
    }

    /**
     * @param int $page
     */
    public function setPage(int $page)
    {
        $this->page = $page;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage(int $perPage)
    {
        $this->perPage = $perPage;
    }
}