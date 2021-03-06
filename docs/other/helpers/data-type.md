# Data Type
Determine the data type of a variable according to Rubix ML's type system.

### Determining Data Type
To determine the integer-encoded data type of a variable:
```php
public static determine($variable) : int
```

**Example**

```php
use Rubix\ML\DataType;

var_dump(DataType::determine('adventure'));
```

```sh
int(2)
```

> **Note:** The return value is an integer encoding of the datatype defined as constants on the DataType class.

### Is Type?
Return true if the variable is categorical:
```php
public static isCategorical($variable) : bool
```

Return true if the variable is categorical:
```php
public static isContinuous($variable) : bool
```

Return true if the variable is a PHP resource:
```php
public static isResource($variable) : bool
```

Return true if the variable is an unrecognized data type:
```php
public static isOther($variable) : bool
```

**Example**

```php
var_dump(DataType::isContinuous(16));

var_dump(DataType::isContinuous(0.928346));

var_dump(DataType::isCategorical(18));

var_dump(DataType::isCategorical('outdoors'));

var_dump(DataType::isCategorical('16'));
```

```sh
bool(true)

bool(true)

bool(false)

bool(true)

bool(true)
```

### Convert Type to String
To return a string representation of an integer encoded data type:
```php
public static asString(int $type) : string
```

**Example**

```php
var_dump(DataType::asString(DataType::CATEGORICAL));
```

```sh
string(11) "categorical"
```