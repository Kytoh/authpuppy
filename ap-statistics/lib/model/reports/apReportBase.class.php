<?php
/**
 * apReportBase
 * base report for all report classes
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
abstract class apReportBase 
{
  protected $formname = "apReportFormBase";
  protected $form = null;
  protected $query = null;
  protected $tablename = null;
  protected $options = array();
  protected $values;
  
  public function __construct($options) {
    foreach ($options as $key => $value) {
      $this->options[$key] = $value;
    }
  }
  
  /**
   * Returns the form to select statistics information
   * @return apReportFormBase
   */
  public function getForm() {
    if (is_null($this->form)) {
      if (class_exists($this->formname)) {
        $formname = $this->formname;
        $this->form = new $formname();
        $this->form->setDefaults($this->options);
      } else {
        throw new apReportException("Form associated with report " . get_class($this) . ", " . $this->formname . " does not exist");
      }
    }
    return $this->form;
  }
  
  /**
   * Returns the model table for this report
   * @return Doctrine_Table
   */
  public function getTable() {
    return Doctrine::getTable($this->tablename);
  }
  
  public function getOption($optionname, $default = '') {
    if (isset($this->options[$optionname])) 
      return $this->options[$optionname];
    else
      return $default;
  }
  
  
  /**
   * Function that builds the query object to run.  Should be implemented in every report
   * @return Doctrine_Query
   */
  abstract protected function buildQuery();
  
  /**
   * Function that returns whether we have sufficient information to generate a report
   * That's where we typically verify the values entered by the form.
   * 
   * It returns true by default, but should be overridden if a report needs some values
   *   
   * @return boolean
   */
  protected function canGenerate() {
    return true;
  }
  
  /**
   * Returns the fields to fetch from the object
   * By default, returning an empty array means all fields will be returned
   */
  protected function getFieldList() {
    return array(); 
  }
  
  protected function recurseGetData($object, &$data = array()) {
    $data2 = $object->getData();
    $data = array_merge($data2, $data);
    foreach ($object->getReferences() as $reference) {
      $data = $this->recurseGetData($reference, $data);
    }
    return $data;
  }
  
  /**
   * Extracts the requested fields for the report
   * @param Doctrine_Record $object
   */
  protected function extractFields($object) {
    $data = $this->recurseGetData($object);
    $fields = $this->getFieldList();

    // By default return all fields
    if (empty($fields)) {
      return $data;
    } else {
      $values = array();
      foreach ($fields as $field) {
        // If the field is as is in the data, just copy it
        if (isset($data[$field])) {
          $values[$field] = new apReportValue($data[$field], array('model' => $object, 'key' => $field));
        } else {
          try {
            // See if the field is available as is
            $values[$field] = new apReportValue($object->$field, array('key' => $field, 'model' => $object));
          } catch (Exception $e) {
            // Otherwise, there may be a method to generate it, so we need to camelcase the field name    
            $fieldparts = explode('_', $field);
            $fieldparts = array_map('ucfirst', $fieldparts);
            $function = "get" . implode('', $fieldparts);
            if (method_exists($object, $function)) {
              $values[$field] = new apReportValue($object->$function(), array('model' => $object, 'key' => $field));
            } else
              // If it is not a method, then leave it blank
              $values[$field] = new apReportValue('');
          }
        }
      }
      return $values;
    }
  }
  
  /**
   * Generates the actual report, given the values selected by the user
   * @return array of array of array of rows (many reports can be generated from the same query, though one is usually the cas
   */
  public function  generate() {
    
    // First we need to know if we have sufficient information to generate the report
    if ($this->canGenerate()) {
      // Now we build the query, each report should implement that function
      $query = $this->buildQuery();
      $results = $query->execute();
      
      return $this->buildReports($results);
    }
    
  }
  
  /**
   * Build the actual array of data for the report.  
   * There can be many reports for a same class, so this returns them all
   * By default, one report is built with one row per resultset
   * @param array of results $results
   * @param string $rptname The name of the report to generate
   * @return array of array of array of rows (many reports can be generated from the same query, though one is usually the cas
   */
  protected function buildReports($results, $rptname = 'detail') {
    $values = array();
    foreach ($results as $result_object) {
      $values[] = $this->extractFields($result_object);
    }
    $this->values = array($values);
    return array($rptname => $values);
  }
  
  public function getSum($key, $reportname = '') {
    $reports = $this->values;
    if (isset($reports[$reportname])) {
      $reports = array($reports[$reportname]);
    }
    foreach ($reports as $rptData) {
      $first = array_shift($rptData);
      if (isset($first[$key])) {
        
        $rptValue = $first[$key];
        if ($rptValue->isSummable()) {
          $sum = $rptValue->getValue();
          // Sum all the values
          foreach ($rptData as $data) {
            $rptValue = $data[$key];
            $sum += $rptValue->getValue();
          }
          return new apReportValue($sum, array('type' => $rptValue->getType()));
        }
      }
    }
    return '';
  }
  
}