sfDoctrineActAsLocalizablePlugin
================================

This is the introduction to the Localizable Doctrine extension which gives you  
functionality to convert units of measurement or any other conversions desirable. 
Below you will find all the documentation on how to install and use the Locatable 
extension.

Using the `Localizable` behavior is simple. If you specify the value for a specific field
identified as localizable, you can immediately get all the conversions available for that
field.

Define our model that uses the behavior included.

    [php]
    class LocalizableUnit extends Doctrine_Record
    {
        public function setTableDefinition()
        {
            $this->hasColumn('name', 'string', 255);
        }

        public function setUp()
        {
            $this->actAs('Localizable', array(
                'fields' => array(
                  'distance' => 'KM'),
            ));
        }
    }

This essentially creates a field on your model, and specifies the unit of measurement
to save to the database.  This is important for precision reasons.
Now we can use it like the following.
    
    [php]
    $unit = new LocatableUnit();
    $article->name      = "Testing this out";
    $article->length    = 15;  // sets unit to 15 KM
    echo $article->length['KM']; // outputs '15'
    echo $article->length['MI'];  // outputs '9.32056'
    
    $article->length['M']    = 1000; // you can set the value for any unit
    echo $article->length;        // outputs '1', still outputs in kilometers
    echo $article->length['ft'];  // outputs '3 280.8399'
    echo $article->length->ft;    // outputs '3 280.8399', Property accessors also work
         
    $article->save();             // the value '1' will be saved to the database

    print_r($article->toArray());

Now that article would output the following.

    Array
    (
      [id] => 1
      [name] => Testing this out
      [length_km] => 1
    )

As many fields can act as localizable as you need.  You can also pass a conversion array in your declaration to add
unsupported conversions

    [php]
    class LocalizableUnit extends Doctrine_Record
    {
        public function setTableDefinition()
        {
            $this->hasColumn('name', 'string', 255);
        }

        public function setUp()
        {
            $this->actAs('Localizable', array(
                'fields' => array(
                  'distance' => 'KM'),
                'conversions' => array(
                  'nautical_miles' => array('km' => 1.85200, 'mi' => 1.15077945),
                  'km'  => array('nautical_miles' => .539956803),
                  'mi'  => array('nautical_miles' => .868976242)),
            ));
        }
    }
    
    $unit = new LocatableUnit();
    $article->name      = "Testing this out";
    $article->length    = 15;  // sets unit to 15 KM
    echo $article->length->nautical_miles; // outputs '8.09935205'
    
The conversion array passed to the behavior supports conversion from miles and kilometers to nautical miles and vice versa.

You can also use the `LocalizableConverter` object to perform conversions outside of the extension

    [php]
    $converter = new LocalizableConverter($additional_conversions);
    $celcius = $converter->convert($fahrenheit, 'F', 'C');
    $gallons = $liters * $converter->getConversion('L', 'GAL');
    
