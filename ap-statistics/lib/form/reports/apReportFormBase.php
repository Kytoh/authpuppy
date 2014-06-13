<?php

/**
 * apStatisticsFormBase
 * base form for statistics allowing to set various types of widgets.  In itself, 
 *   it has no widgets, it just provides an easy to use way to add widgets as necessary
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apReportFormBase extends BaseForm
{
  protected $namespace = 'stats';
  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();
    $this->widgetSchema->setNameFormat($this->namespace . '[%s]');   
    
  }
  
  /**
   * Adds two date widgets to select a range to/from date
   * @param string $name name of the widgets will be $name_from and $name_to
   * @param boolean $required is the field required or not
   */
  public function addDateRange($name = "range", $required = false) {
    $this->widgetSchema[$name] = new sfWidgetFormDateRange(array('from_date' => new sfWidgetFormJQueryDate(), 'to_date' => new sfWidgetFormJQueryDate()));
    $this->validatorSchema[$name] = new sfValidatorDateRange(array('from_date' => new sfValidatorDate(array('required' => $required)),
                      'to_date' => new sfValidatorDate(array('required' => $required))), array('invalid' => "The 'from' date must be earlier than 'to' date"));
  }
  
  public function flattenList($array, $parent = "") {
    $list = array();
    foreach ($array as $key => $value) {
      if (is_array($value)) {
        $list = array_merge($list, $this->flattenList($value, $key));
      } else {
        $list[$parent != '' ? $parent . ":" . $key : $key] = $parent != '' ? $parent . ":" . $value : $value;
      }
    }
    return $list;
  }
  
  public function addList($name = "list", $list = array()) {
    $this->widgetSchema[$name] = new sfWidgetFormChoice(array('multiple' => true, 'expanded' => false, 'choices' => $this->flattenList($list)), array('size' => 10));
    $this->validatorSchema[$name] = new sfValidatorChoice(array('required' => false, 'multiple' => true, 'choices' => array_keys($this->flattenList($list))));
  }
  
  public function addDoctrineList($name = "dlist", $modelclass = null, $options = array()) {
    if (!is_null($modelclass)) {
      $this->widgetSchema[$name] = new sfWidgetFormDoctrineChoice(array_merge($options, array('model' => $modelclass, 'order_by' => array('name', 'asc'), 'multiple' => true)), array('size' => 10));
      $this->validatorSchema[$name] = new sfValidatorDoctrineChoice(array_merge($options, array('model' => $modelclass, 'multiple' => true, 'required' => false)));
    }
  }
  
  public function addNodeList($name = "nodes") {
    $this->addDoctrineList($name, 'Node');
    
  }
  
  /**
   * Adds a list of model fields for a given model
   * @param string $modelname The model class to get fields for
   * @param array $extrafields a list of fields that are not from the object but are available from the report query
   * @param string $name widget name
   */
  public function addFieldList($modelname, $extrafields = array(), $name = "fields") {
    $fields = $extrafields;
    if (class_exists($modelname)) {
      $class = new $modelname();
      if ($class instanceof sfDoctrineRecord) {
        $modelfields = $class->getData();
        foreach($modelfields as $field => $value)
          if ($field != 'id')
            $fields[$field] = $field;
        
      }
    }
    $this->addList($name, $fields);
  }
  
  /**
   * Adds a simple text field to the form
   * @param string $name  The name of the field
   * @param boolean $required  Whether the field is required or not
   */
  public function addTextField($name = "text", $required = false) {
    $this->widgetSchema[$name] = new sfWidgetFormInputText();
    $this->validatorSchema[$name] = new sfValidatorString(array('required' => $required));

  }
  
  public function getNameFormat() {
    return $this->namespace;
  }
  
  public function setDefaults($defaults) {
    $realdefaults = array();
    foreach ($defaults as $key => $default) {
      if (isset($this->widgetSchema[$key]))
        $realdefaults[$key] = $default;
    }
    // Add a default date range
    if (!isset($realdefaults["range"]) && isset($this->widgetSchema["range"])) {
      $realdefaults["range"] = array("from" => date('Y-m-d', strtotime('-1 month')), "to" => date('Y-m-d'));
    }
    parent::setDefaults($realdefaults);
  }
  
  public function getValues() {
    $values = parent::getValues();
    $defaults = $this->getDefaults();
    return self::deepArrayUnion($defaults, $values);
  }

}