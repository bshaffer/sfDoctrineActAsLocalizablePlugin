<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

/**
 * Doctrine_Template_Localizable_TestCase
 *
 * @package     Doctrine
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 */
class Doctrine_Template_Localizable_TestCase extends Doctrine_UnitTestCase 
{
    public function prepareTables()
    {
        $this->tables[] = "LocalizableUnit_KG";
        $this->tables[] = "LocalizableUnit_KM";
        $this->tables[] = "LocalizableUnit_IN";
        $this->tables[] = "LocalizableUnit_C";        
        parent::prepareTables();
    }
    
    public function testLocalizableTable()
     {
       $article = new LocalizableUnit_KG();
       $this->assertTrue($article->getTable()->hasColumn('weight_kg'));
       
       // Make sure the Localizable filter throws an error on unspecified properties
        try {
          $fake = $article->unit;        // GET
          $this->fail();
        } 
        catch (Doctrine_Record_UnknownPropertyException $e) {
          $this->pass();
        }
        try {
         $article->unit = 45;           // SET
         $this->fail();
        } 
        catch (Doctrine_Record_UnknownPropertyException $e) {
         $this->pass();
        }
       
     }
     
     public function testLocalizableProperty()
     {   
         $article = new LocalizableUnit_KG();
         $article->name  = 'Testing out assigning to the property without conversion';
         
         $article->weight = 35;
         $article->save();
         $article = Doctrine::getTable('LocalizableUnit_KG')->find($article->id);  
               
         $this->assertEqual($article->weight, '35');
         $this->assertEqual((string)$article->weight, 35);
         
         // Custom __toInt() functions not supported in PHP
         // $this->assertEqual($article->weight, 35);           
     }
     
    public function testLocalizableArrayAccess()
    {   
        $article = new LocalizableUnit_KG();
        $article->name  = 'Testing out Array Assignment';
        
        $unit = $article->weight;
        $unit['KG'] = 35;
        $article->weight = $unit;
        $this->assertEqual($unit['KG'], '35');
        $this->assertEqual($article->weight['KG'], '35');
        
        $article->weight['KG'] = 36;
        $unit = $article->weight;
        $this->assertEqual($unit['KG'], '36');

        $article->weight['KG'] = 37;
        $this->assertEqual($article->weight['KG'], '37');
        
        
        // Make sure the changes are persistent
        $article = new LocalizableUnit_KG();
        $article->name  = 'Testing out Array Assignment Persistence';
        
        $unit = $article->weight;
        $unit['KG'] = 45;
        $article->weight = $unit;
        $article->save();
        $this->assertEqual($article->weight['KG'], '45');
        $saved = Doctrine::getTable('LocalizableUnit_KG')->find($article->id);  
        $this->assertEqual($saved->weight['KG'], '45');
                
        $article->weight['KG'] = 46;
        $article->save();
        $this->assertEqual($article->weight['KG'], '46');
        $saved = Doctrine::getTable('LocalizableUnit_KG')->find($article->id);  
        $this->assertEqual($saved->weight['KG'], '46');
    }

    public function testLocalizablePropertyAccess()
    {   
        $article = new LocalizableUnit_KG();
        $article->name  = 'Testing out Property Assignment';
        
        $unit = $article->weight;
        $unit['KG'] = 35;
        $article->weight = $unit;
        $this->assertEqual($unit->KG, '35');
        $this->assertEqual($article->weight->KG, '35');
        
        $article->weight->KG = 36;
        $unit = $article->weight;
        $this->assertEqual($unit->KG, '36');

        $article->weight->KG = 37;
        $this->assertEqual($article->weight->KG, '37');
        
        
        // Make sure the changes are persistent
        $article = new LocalizableUnit_KG();
        $article->name  = 'Testing out Property Assignment Persistence';
        
        $unit = $article->weight;
        $unit->KG = 45;
        $article->weight = $unit;
        $article->save();
        $this->assertEqual($article->weight->KG, '45');
        $saved = Doctrine::getTable('LocalizableUnit_KG')->find($article->id);  
        $this->assertEqual($saved->weight->KG, '45');
                
        $article->weight->KG = 46;
        $article->save();
        $this->assertEqual($article->weight->KG, '46');
        $saved = Doctrine::getTable('LocalizableUnit_KG')->find($article->id);  
        $this->assertEqual($saved->weight->KG, '46');
    }

    
    public function testLocalizableConversion()
    { 
      $article = new LocalizableUnit_KM();
      $article->name  = 'Testing out Conversion upon assignment';
    
      $article->distance = 5; //five kilometers
      $miles = $article->distance['MI'];
      $this->assertEqual($miles, '3.106855');
      $this->assertEqual($miles, 3.106855); // Converts to Int

      $article = new LocalizableUnit_KM();
      $article->name  = 'Testing out Conversion upon different unit assignment';
    
      $article->distance['MI'] = 30; //five kilometers
      $this->assertEqual($article->distance, '48.28032');     // Returns KM by default
      $this->assertEqual($article->distance['m'], 48280.32);  // Also converts to meters 


      $article = new LocalizableUnit_KM();
      $article->name  = 'Testing out Conversion Persistence';
    
      $article->distance['in'] = 36; // 30 inches
      $article->save();
      $this->assertEqual($article->distance['km'], '.0009144');     // Saved correct KM value
      $this->assertEqual(round($article->distance['ft'], 5), 3);    // Also converts to feet
    }
    
    public function testLocalizableDelthas()
    { 
      $article = new LocalizableUnit_C();
      $article->name  = 'Testing out Temperature Conversion';

      $article->temperature = 20; // room tempurature Celcius
      $this->assertEqual($article->temperature['C'], '20');
      $this->assertEqual($article->temperature['K'], '293.15');
      $this->assertEqual($article->temperature['F'], '68');
      
      $article->temperature['F'] = 80;
      $this->assertEqual($article->temperature, '26.666666');
      $this->assertEqual(round($article->temperature['C'], 5), 26.66667);
      $this->assertEqual(round($article->temperature['K'], 5), 299.81667);
    }
}

class LocalizableUnit_KG extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('name',  'string',   255);
    }

    public function setUp()
    {
        $this->actAs('Sluggable', array('fields' => array('name')));
        $this->actAs('Localizable', array(
            'fields' => array('weight' => 'KG')
        ));
    }
}

class LocalizableUnit_KM extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('name',  'string',   255);
    }

    public function setUp()
    {
        $this->actAs('Sluggable', array('fields' => array('name')));
        $this->actAs('Localizable', array(
            'fields' => array('distance' => 'KM')
        ));
    }
}

class LocalizableUnit_IN extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('name',  'string',   255);
    }

    public function setUp()
    {
        $this->actAs('Sluggable', array('fields' => array('name')));
        $this->actAs('Localizable', array(
            'fields' => array('length' => 'in'),
            'precision' => 2
        ));
    }
}

class LocalizableUnit_C extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('name',  'string',   255);
    }

    public function setUp()
    {
        $this->actAs('Sluggable', array('fields' => array('name')));
        $this->actAs('Localizable', array(
            'fields' => array('temperature' => 'C')
        ));
    }
}
