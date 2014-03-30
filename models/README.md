Models
===========

I use these classes as simple ORMs. Sometimes I have to work with legacy code with old PHP versions, so I wrote these classes

## TableModel ##
Lets you interact with a table in the DB like it was a class.

There are some limitations, but if I had to do something that was more 'fancy' I'd use one of the many ORMs out there


### Usage example ###

Load `Examples` somewhere and take a look at it!

    $foo = new TableExample();
    $foo->value = 'Some value';
    $foo->save();
    
    echo $foo->value; // echoes 'Some value'
    echo $foo->ID; // should echo '1', if it's the first inserted value
    
    // later on
    $foo = new TableExample(1);
    echo $foo->value; // echoes 'Some value'
    
    $foo->value = 'Updated!';
    echo $foo->value; // echoes 'Updated!'
    
    $foo->save();
    echo $foo->value; // echoes 'Updated!'
