<?php
declare(strict_types=1);


namespace Dengpju\PhpGen\Entity;


use Dengpju\PhpGen\Utils\HumpUtil;

abstract class BaseEntity
{
    private bool $isEdit = false;
    protected array $flags = [];
    protected array $initFlags = [];

    public null|string $createTime = null;
    public null|string $updateTime = null;
    public null|string $deleteTime = null;

    const CREAT_TIME = 'createTime';
    const UPDATE_TIME = 'updateTime';
    const DELETE_TIME = 'deleteTime';
    const EXCEPT = [
        self::CREAT_TIME,
        self::UPDATE_TIME,
        self::DELETE_TIME,
    ];
    public array $batchData = [];

    /**
     * BaseEntity constructor.
     * @param bool $isEdit 是否为编辑
     * @throws \ReflectionException
     */
    public function __construct(bool $isEdit = false)
    {
        $this->isEdit = $isEdit;
        $this->createTime = date('Y-m-d H:i:s');
        $this->updateTime = $this->createTime;
        $this->deleteTime = $this->createTime;
        $this->instanceEntity();
        $this->setInitFlags();
    }

    /**
     * @return bool
     */
    public function getIsEdit(): bool
    {
        return $this->isEdit;
    }

    /**
     * @param bool $isEdit
     */
    public function setIsEdit(bool $isEdit)
    {
        $this->isEdit = $isEdit;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if ($this->flags() && isset($this->flags()[$name])) {
            $this->setIsEdit(true);
            foreach ($this->flags as $property => $flag) {
                unset($this->{$property});
            }
            $this->{$name} = $value;
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->flags[$name])) {
            return $this->flags[$name];
        }
    }

    /**
     * 映射属性值
     * @param array $propertys
     */
    public function mapper(array $propertys)
    {
        foreach ($propertys as $property => $value) {
            $property = HumpUtil::key($property);
            if (!in_array($property, self::EXCEPT) && property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    /**
     * key 转驼峰
     * @param array $content
     * @return array|mixed|null|string|string[]
     */
    protected function hump(array $content)
    {
        $hump = [];
        if ($content) {
            $hump = preg_replace_callback('/[_]([a-zA-Z])(?=[^"]*?":)/',
                function ($matches) {
                    return strtoupper($matches[1]);
                }, json_encode($content));
            $hump = json_decode($hump, true);
        }
        return $hump;
    }

    /**
     * @return array
     */
    private function flags(): array
    {
        $this->flags = $this->initFlags;
        return $this->flags;
    }

    private function setInitFlags()
    {
        $refClass = new \ReflectionClass($this);
        $classDoc = $refClass->getDocComment();
        preg_match_all('/(?:(\@property\s+bool\s+[(\$flag)|(flag)])).*?(?=(' . PHP_EOL . '))/', $classDoc, $doc);
        if ($doc[0]) {
            $doc = json_decode(str_replace("\\r", '', json_encode($doc[0])));
            foreach ($doc as $key => $flag) {
                $flag = preg_replace("/\@property\s+bool\s+/", "", $flag);
                $flag = preg_replace("/[\x{4e00}-\x{9fa5}\s+\$+]+/u", "", $flag);
                $this->initFlags[trim(trim($flag, PHP_EOL . "'"), "'")] = false;
            }
        }
    }

    /**
     * 实例化实体
     * @throws \ReflectionException
     */
    private function instanceEntity()
    {
        $refThis = new \ReflectionClass($this);
        foreach ($this as $property => $value) {
            if (preg_match("/Entity$/", $property)) {
                if ($refThis->getName() !== $refThis->getNamespaceName() . "\\" . $property) {
                    $refClass = new \ReflectionClass($refThis->getNamespaceName() . "\\" . $property);
                    $this->{$property} = $refClass->newInstance();
                }
            }
        }
    }

    public function toArray(): array
    {
        return json_decode(json_encode($this), true);
    }
}