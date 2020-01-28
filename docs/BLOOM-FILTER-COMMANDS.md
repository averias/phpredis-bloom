# `Bloom Filter` Commands
You can execute Bloom Filter commands in two ways:

**With RedisBloomClient**

First, you need to create a RedisBloomClient from RedisBloomFactory, and then execute the command from the client. 
You need to specify the name of the filter, as key param, in each command. You can execute BloomFilter commands on 
different filters (keys) using RedisBloomClient. All BloomFilter commands signatures in RedisBloomClient are prefixed 
with `bloomFilter`, like `bloomFilterAdd` or `bloomFilterMultiExists`.

```
$factory = new RedisBloomFactory();
$client = $factory->createClient();
$client->bloomFilterAdd('filter-key', 'item');
```

**With BloomFilter class**

You can create a BloomFilter object by instantiating it from RedisBloomFactory and then execute all BloomFilter commands
over one filter which is specified as key param when you create the BloomFilter object.

```
$factory = new RedisBloomFactory();
$bloomFilter = $factory->createBloomFilter('filter-key');
$bloomFilter->add('item');
```

Both RedisBloomClient and BloomFilter object can be configured with a specific connection to Redis when they are created by providing
a configuration array to `RedisBloomFactory::createClient(array $config)` or 
`RedisBloomFactory::createBloomFilter(string $filterName, array $config)`, even you can provide a configuration array to
RedisBloomFactory, `RedisBloomFactory(array $config)`, and all clients and BloomFilter objects created by the factory 
will be using that configuration. Please take a look at `examples/factory.php` to know how to provide configuration options.

## Commands
It is highly recommended you read the full documentation of the commands in [RedisBloom - Bloom Filter Command Documentation](https://oss.redislabs.com/redisbloom/Bloom_Commands/) 
for a better understanding of how Bloom Filters work.

### `Reserve`
Creates an empty Bloom Filter with a single sub-filter for the initial capacity requested and with an upper bound 
`error_rate`. By default, the filter auto-scales by creating additional sub-filters when `capacity` is reached. The
new sub-filter is created with size of the previous sub-filter multiplied by `expansion` optional param.

`$redisBloomClient->bloomFilterReserve(string $key, float $errorRate, int $capacity, array $options = []);`

or

`$bloomFilter->reserve(float $errorRate, int $capacity, array $options = []);`

**Params:**
- key: (string) filter name
- errorRate: (float) probability for false positives. 0.0 < errorRate < 1.0
- capacity: (int) number of entries intended to be added to the filter
- options: (array) optional, if specified it can contain:
    * EXPANSION: (int) when `capacity` is reached, an additional sub-filter is created. The size of the new sub-filter is the size of the last sub-filter multiplied by `expansion`. If the number of elements to be stored in the filter is unknown, we recommend that you use an `expansion` of 2 or more to reduce the number of sub-filters. Otherwise, we recommend that you use an `expansion` of 1 to reduce memory consumption. The default expansion value is 2.
    * NONSCALING:(bool), if specified and equal to true it prevents the filter from creating additional sub-filters if initial capacity is reached. Non-scaling filters requires slightly less memory than their scaling counterparts. The filter returns an error when `capacity` is reached.

**NONSCALING and EXPANSION combination is not allowed**

```
$factory = new RedisBloomFactory();
$client = $factory->createClient();
$options = [
   OptionalParams::EXPANSION => 4,
   OptionalParams::NON_SCALING => true
];

// it will create a Bloom Filter with 0.1 error rate and 100 of capacity 
// since NON_SCALING = true, EXPANSION will be ignored since no sub-filters
//  will be created
$client->bloomFilterReserve('test-filter', 0.1, 1000, $options);

```

**Returns:** (bool) true if the filter was created, otherwise `ResponseException`.

### `Add`
Adds an item to the Bloom Filter, creating the filter if it does not yet exist.

`$redisBloomClient->bloomFilterAdd(string $key, $item);`

or

`$bloomFilter->add($item);`

**Params:**
- key: (string) filter name
- item: (string|number) scalar value to add

**Returns:** (bool) true if the item was added to the filter, false if the item may have existed previously. 
`ResponseException` if item is not string or number.

### `MultiAdd`
Adds one or more items to the Bloom Filter, creating the filter if it does not exist yet.

`$redisBloomClient->bloomFilterMultiAdd(string $key, ...$items);`

or

`$bloomFilter->multiAdd(...$items);`

**Params:**
- key: (string) filter name
- items: comma-separated list of (string|number) scalar values to add

**Returns:** (array) of true/false values, indicating if each item (in the position which was inserted) was added to 
the filter or may have existed previously.`ResponseException` if some of the items are not string or number.

### `Insert`
It is a sugarcoated combination of `Reserve` and `Add`. It creates a new filter if the `key` does not exist using the 
relevant arguments (see `Reserve`), then, all `items` are added.

`$redisBloomClient->bloomFilterInsert(string $key, array $items, array $options = []);`

or

`$bloomFilter->insert(array $items, array $options = []);`

**Params:**
- key: (string) filter name
- items: (array) of (string|number) scalar values to be added
- options: (array) optional, if specified it can contain up to 3 params:
    * ERROR: (float) 0.0 < errorRate < 1.0. If specified, it indicates the `error` ratio of the newly created filter if it does not exist yet. If the filter is automatically created and `error` is not specified then the module-level error rate is used
    * CAPACITY: (int) if specified, it indicates the desired `capacity` for the filter to be created (see `Reserve`). This parameter is ignored if the filter already exists. If the filter is automatically created and this parameter is absent, then the module-level `capacity` is used
    * NOCREATE: (bool) if specified and equal to true indicates that the filter should not be created if it doesn't already exist. If the filter does not exist yet, an error is returned rather than creating it automatically. This may be used where a strict separation between filter creation and filter addition is desired. It is an error to specify `NOCREATE` together with either `CAPACITY` or `ERROR`
    * EXPANSION: (int) if specified, when `capacity` is reached, an additional sub-filter is created. The size of the new sub-filter is the size of the last sub-filter multiplied by `expansion`. If the number of elements to be stored in the filter is unknown, we recommend that you use an `expansion` of 2 or more to reduce the number of sub-filters. Otherwise, we recommend that you use an `expansion` of 1 to reduce memory consumption. The default expansion value is 2
    * NONSCALING:(bool) if specified and equal to true it prevents the filter from creating additional sub-filters if initial capacity is reached. Non-scaling filters requires slightly less memory than their scaling counterparts. The filter returns an error when `capacity` is reached

**NONSCALING and EXPANSION combination is not allowed**

```
$factory = new RedisBloomFactory();
$client = $factory->createClient();
$options = [
   OptionalParams::CAPACITY => 1000,
   OptionalParams::ERROR => 0.01, 
   OptionalParams::EXPANSION => 4,
   OptionalParams::NO_CREATE => true
];

// it will insert 'foo', 'bar' and 18 values to filter 'test-filter' in case it already exists 
// since NO_CREATE = true, otherwise it will send and ResponseException
$client->bloomFilterInsert('test-filter', ['foo', 'bar', 18], $options);

```

**Returns:** (array) of true/false values, indicating if each item (in the position which was inserted) was added to 
the filter or may have existed previously.`ResponseException` if some of the items are not string or number or in case 
we specify `NO_CREATE` = true and the filter does not exists.

### `Exists`
Determines whether an item may exist in the Bloom Filter or not.

`$redisBloomClient->bloomFilterExists(string $key, $item);`

or

`$bloomFilter->exists($item);`

**Params:**
- key: (string) filter name
- item: (string|number) scalar value to add

**Returns:** (bool) true if the item may exist in the filter, false if either the item doesn't exist in the filter or 
the filter doesn't exist. 

### `MultiExists`
Determines if one or more items may exist in the filter or not.

`$redisBloomClient->bloomFilterMultiExists(string $key, ...$items);`

or

`$bloomFilter->multiExists(...$items);`

**Params:**
- key: (string) filter name
- items: comma-separated list of (string|number) scalar values to add

**Returns:** (array) of true/false values, indicating if each item (in the position which was inserted) may exist in 
the filter or does not exist. `ResponseException` if some of the items are not string or number.

### `ScanDump`
It iterates through a filter returning a chunk of data in each iteration. The first time this command is called, 
the value of the `iterator` should be 0. This command returns a successive array of `[iterator, data]` until iterator = 0 
and data = '', `[0, '']` to indicate completion.

`$redisBloomClient->bloomFilterScanDump(string $key, int $iterator);`

or

`$bloomFilter->scanDump(int $iterator);`

**Params:**
- key: (string) filter name
- iterator: (int) iterator value

**Returns:** (array) An array of `[iterator, data]`. The Iterator is passed as input to the next invocation of `ScanDump`. 
If the iterator is 0, it means iteration has completed. The iterator-data pair should also be passed to 
`LoadChunk` when restoring the filter. It throws a `ResponseException` in case `key` doesn't exist

### `LoadChunk`
Restores a filter previously saved using `ScanDump`. This command overwrites any bloom filter stored under `key`. 
Make sure that the bloom filter is not be changed between invocations.


`$redisBloomClient->bloomFilterLoadChunk(string $key, int $iterator, $data);`

or

`$bloomFilter->loadChunk(int $iterator, $data);`

**Params:**
- key: (string) filter name
- iterator: (int) iterator value
- data: data chunk as returned by `ScanDump`

**Returns:** (bool) true on success. It throws a `ResponseException` in case `key` doesn't exist

### `Copy`
Currently, this command is only available in `BloomFilter` class, not in `RedisBloomClient`.

It copies all data stored in the key specified in the `BloomFilter` class into `key` target, basically it combines 
one `scanDump` with a `loadChunk` on the fly in each iteration until all data are consumed from the BloomFilter object 
`key` source and inserted in the target `key`. 

`$bloomFilter->copy(string $targetFilter);`

**Params:**
- targetFilter: (string) destination filter name

**Returns:** (bool) true on success. It throws a `ResponseException` in case of target `key` doesn't exist or an error or 
a failure happens. In case of error, the command will try to delete the target `key` before throwing the exception.

### `Info`
Returns information about the filter stored in the key.

`$redisBloomClient->bloomFilterInfo(string $key);`

or

`$bloomFilter->info();`

**Params:**
- key: (string) filter name

**Returns:** (associative array) with the following structure:
```
[
   'Capacity' => 106, // integer
   'Size' => 218, // integer
   'Number of filters' => 1, // integer
   'Number of items inserted' => 30 // integer
   'Expansion rate' => 4 // integer
]
```

It throws a`ResponseException` if filter key doesn't exist.