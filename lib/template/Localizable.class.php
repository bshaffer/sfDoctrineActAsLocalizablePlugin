<?php

class Doctrine_Template_Localizable extends Doctrine_Template
{    
  /**
   * Array of locatable options
   */  
  protected $_options = array('fields' => array(), 'columns' => array(), 'conversions' => array());
  
  /**
   * Constructor for Locatable Template
   *
   * @param array $options 
   * @return void
   * @author Brent Shaffer
   */
  public function __construct(array $options = array())
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
  }


  public function setup()
  {
  }


  /**
   * Set table definition for contactable behavior
   * (borrowed from Sluggable in Doctrine core)
   *
   * @return void
   * @author Brent Shaffer
   */
  public function setTableDefinition()
  {
    foreach ($this->_options['fields'] as $field => $unit) 
    {
      $name = Doctrine_Inflector::tableize($field.'_'.strtolower($unit));
      $this->_options['columns'][$field] = $name;
      $this->hasColumn($name, 'float');
    }
    $this->_table->unshiftFilter(new Doctrine_Record_Filter_Localizable($this->_options));
  }
}