<?php


namespace App\Models;


use App\Core\Model;
use PDOException;

class Image extends Model
{

    protected $id;
    protected $author;
    protected $path;
    protected $ref;
    protected $newId;

    public function __construct($id = null, $author = '', $path = '', $ref = '')
    {
        $this->id = null;
        $this->newId = $id;
        $this->author = $author;
        $this->path = $path;
        $this->ref = $ref;
    }

    static public function setDbColumns()
    {
        return [
            'id',
            'author',
            'path',
            'ref'
        ];
    }

    static public function setTableName()
    {
        return 'images';
    }

    public function getId()
    {
        return $this->id;
    }

    public function delete()
    {
        $this->ref = $this->ref - 1;
        if ($this->ref == 0) {
            parent::delete();
            unlink($this->path);
        }
    }

    public static function deleteUnlinkedFiles()
    {
        try {
            $images = self::getAll();
        } catch (\Exception $e) {
            return; // TODO
        }

        if (count($images) === 0) {
            return;
        }

        $paths = [];
        $i = 0;
        foreach ($images as $image) {
            $path = explode("/", $image->path);
            $paths[$i++] = end($path);
        }

        $array = scandir("public/visuals/images/");
        $found = false;

        $i = 0;
        foreach ($array as $n) {
            if ($i++ < 2) {
                continue;
            }
            $found = false;
            foreach ($paths as $p) {
                if ($n === $p) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                unlink("public/visuals/images/" . $n);
            }
        }

    }

    /**
     * @throws \Exception if query failed
     */
    public function addReference()
    {
        $this->ref = $this->ref + 1;

        $this->save();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function save()
    {
        parent::connect();
        try {
            $data = array_fill_keys(self::getDbColumns(), null);
            foreach ($data as $key => &$item) {
                $item = isset($this->$key) ? $this->$key : null;
            }
            if ($data[self::$pkColumn] == null) {
                $data[self::$pkColumn] = $this->newId;
                $arrColumns = array_map(fn($item) => (':' . $item), array_keys($data));
                $columns = implode(',', array_keys($data));
                $params = implode(',', $arrColumns);
                $sql = "INSERT INTO " . self::getTableName() . " ($columns) VALUES ($params)";
                $stmt = self::$connection->prepare($sql);
                $stmt->execute($data);
                return $this->newId;
            } else {
                $arrColumns = array_map(fn($item) => ($item . '=:' . $item), array_keys($data));
                $columns = implode(',', $arrColumns);
                $sql = "UPDATE " . self::getTableName() . " SET $columns WHERE id=:" . self::$pkColumn;
                $stmt = self::$connection->prepare($sql);
                $stmt->execute($data);
                return $data[self::$pkColumn];
            }
        } catch (PDOException $e) {
            throw new \Exception('Query failed: ' . $e->getMessage());
        }
    }
}