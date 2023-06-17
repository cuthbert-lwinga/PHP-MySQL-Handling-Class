<?PHP
class TableColumn {
    private $name;
    private $type;
    private $isPrimary;

    public function __construct($name, $type, $isPrimary) {
        $this->name = $name;
        $this->type = $type;
        $this->isPrimary = $isPrimary;
    }

    // Setters
    public function setName($name) {
        $this->name = $name;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setIsPrimary($isPrimary) {
        $this->isPrimary = $isPrimary;
    }

    // Getters
    public function getName() {
        return $this->name;
    }

    public function getType() {
        return $this->type;
    }

    public function getIsPrimary() {
        return $this->isPrimary;
    }
}

?>