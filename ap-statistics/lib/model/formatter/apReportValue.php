<?php
/**
 * apReportValue
 * Formats an output
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apReportValue {
  
  const TYPE_STRING = 'string';
  const TYPE_NUMERIC = 'numeric';
  const TYPE_DURATION = 'duration';
  const TYPE_DATETIME = 'datetime';
  const TYPE_SIZE = "size";
  
  protected $value; // The actual value
  protected $type = self::TYPE_STRING; // The type of this value
  
  public function __construct($value, $parameters = array()) {
    $this->value = $value;
    
    $this->configure($parameters);
  }
  
  /**
   * This function tries to find the type of the value
   * @param unknown_type $parameters
   */
  public function configure($parameters = array()) {
    if (isset($parameters['type'])) {
      $this->type = $parameters['type'];
    } elseif (isset($parameters['model']) && isset($parameters['key'])) {
      // Try to find the type from the data type of the model
      $model = $parameters['model'];
      $key = strtolower($parameters['key']);
      if ($key == 'incoming' || $key == 'outgoing') {
        $this->type = self::TYPE_SIZE;
      }
      elseif ($model instanceof sfDoctrineRecord) {
        if ($coldef = $model->getTable()->getColumnDefinition($key)) {
          switch($coldef['type']) {
            case 'integer':
            case 'float':
            case 'decimal':
                $this->type = self::TYPE_NUMERIC;
                break;
            case 'date':
            case 'time':
            case 'timestamp':
            case 'datetime':
              $this->type = self::TYPE_DATETIME;
              break;
            default:
              $this->type = self::TYPE_STRING;
    
          }
        } else {
          // The key is not a column of the model so we try to guess what it is
          if (is_int($this->value) && (strpos($key, 'duration') !== false)) {
            $this->type = self::TYPE_DURATION;
          } elseif ( (strpos($key, 'date') !== false) || (strpos($key, 'time') !== false) ) {
            $this->type = self::TYPE_DATETIME;
          } elseif (is_int($this->value)) {
            $this->type = self::TYPE_NUMERIC;
          }           
        }
      }
    }
  }
  
  public function formatString() {
    return strval($this->value);
  }
  
  public function formatNumeric() {
    
    return strval($this->value);
  }
  
  public function formatDatetime() {
    $value = $this->value;
    if (is_int($value)) {
      $value = date('Y-m-d H:i:s', $value);
    }
    return $value;
  }
  
  public function formatDuration() {
    $difftime = $this->value;
     
    return apUtils::displayDuration($difftime);
  }
  
  public function formatSize() {
     
    $value = $this->value;
      $value = apUtils::size_readable($value);
    return $value;
  }
  
  public function __toString() {
    $fct = 'format' . ucfirst($this->type);
    if (method_exists($this, $fct)) {
      return $this->$fct();
    }
    return strval($this->value);
  }
  
  public function isSummable() {
    return ($this->type == self::TYPE_NUMERIC || ($this->type==self::TYPE_DURATION && is_int($this->value)) || ($this->type==self::TYPE_SIZE ) );
  }
  
  public function getValue() {
    $value = $this->value;
    switch ($this->type) {
      case self::TYPE_NUMERIC:
      case self::TYPE_SIZE:
        if (!is_numeric($value)) {
          $value = intval($value);
        }
        break;
    }
    return $value;
  }
  
  public function getType() {
    return $this->type;
  }
  
}