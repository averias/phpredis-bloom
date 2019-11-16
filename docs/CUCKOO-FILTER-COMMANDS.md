# `Cuckoo Filter` Commands
You can execute Cuckoo Filter commands in two ways:

**With RedisBloomClient**

First, you need to create a RedisBloomClient from RedisBloomFactory, and then execute the command from the client. 
You need to specify the name of the filter, as key param, in each command. You can execute BloomFilter commands on 
different filters (keys) using RedisBloomClient. All CuckooFilter commands signatures in RedisBloomClient are prefixed 
with `cuckooFilter`, like `cuckooFilterDelete` or `cuckooFilterCount`.

```
$factory = new RedisBloomFactory();
$client = $factory->createClient();
$client->cuckooFilterAdd('filter-key', 'item');
```

**With CuckooFilter class**

You can create a CuckooFilter object by instantiating it from RedisBloomFactory and then execute all CuckooFilter commands
over one filter which is specified as key param when you create the BloomFilter object.

```
$factory = new RedisBloomFactory();
$cuckooFilter = $factory->createCuckooFilter('filter-key');
$cuckooFilter->add('item');
```

Both RedisBloomClient and CuckooFilter object can be configured with a specific connection to Redis when they are created by providing
a configuration array to `RedisBloomFactory::createClient(array $config)` or 
`RedisBloomFactory::createCuckooFilter(string $filterName, array $config)`, even you can provide a configuration array to
RedisBloomFactory, `RedisBloomFactory(array $config)`, and all clients and CuckooFilter object created by the factory 
will be using that configuration. Please take a look at `examples/factory.php` to know how to provide configuration options.

## Commands
You can find a detailed description of each command in [RedisBloom - Cuckoo Filter Command Documentation](https://oss.redislabs.com/redisbloom/Cuckoo_Commands/).

### `Reserve`
Creates an empty Cuckoo Filter with a given desired initial capacity and some optional params.

`$redisBloomClient->cuckooFilterReserve(string $key, int $capacity, array $options = []);`

or

`$cuckooFilter->reserve(int $capacity, array $options = []);`

**Params:**
- key: (string) filter name
- capacity: (int) estimated capacity for the filter
- options: (array) optional, if specified it can contain up to 3 params:
    * bucketSize: (int) number of items in each bucket. Higher bucket size value improves the fill rate but result in a higher error rate and slightly slower operation speed
    * maxIterations: (int) Number of attempts to swap buckets before declaring filter as full and creating an additional filter. A low value is better for speed while a higher number is better for filter fill rate
    * expansion: (int) when a new filter is created, its size will be the size of the current filter multiplied by expansion

**Returns:** (bool) true if the filter was created. It throws a `ResponseException` if filter already exists or optional 
params are not integer.

### `Add`
Adds an item to the Cuckoo Filter, creating the filter if it does not yet exist, you can add the same item multiple times.

`$redisBloomClient->cuckooFilterAdd(string $key, $item);`

or

`$cuckooFilter->add($item);`

**Params:**
- key: (string) filter name
- item: (string|number) scalar value to add

**Returns:** (bool) true if the item was added to the filter, `ResponseException` if item is not string or number.

### `Add if not exist`
Similar to `Add` but just adds the item if it does not exist previously.

`$redisBloomClient->cuckooFilterAddIfNotExist(string $key, ...$items);`

or

`$cuckooFilter->adIfNotExist(...$items);`

**Params:**
- key: (string) filter name
- items: comma-separated list of (string|number) scalar values to add

**Returns:** (bool) true if the was added successfully because it does not exist previously, false if the item could not 
be added because it already exist.`ResponseException` if some of the items are not string or number.

### `Insert`
Adds one or more items to the Cuckoo Filter, creating the filter if it does not yet exist. You can specify extra 
optional params for setting capacity and no creation in case of no filter existence.

`$redisBloomClient->cuckooFilterInsert(string $key, array $items, array $options = []);`

or

`$cuckooFilter->insert(array $items, array $options = []);`

**Params:**
- key: (string) filter name
- items: (array) of (string|number) scalar values
- options: (array) optional, if specified it can contain up to 3 params:
    * capacity: (int) if specified set the number of entries you intend to add to the filter, if the filter already exists this value will be ignored
    * noCreate: (bool) if specified and equel to true, prevents automatic filter creation if the filter does not exist. Instead, an error will be returned if the filter does not already exist

```
$factory = new RedisBloomFactory();
$client = $factory->createClient();
$options = [OptionalParams::CAPACITY => 1000, OptionalParams::NO_CREATE => true];

// it will insert 'foo', 'bar', and 18 values to filter 'test-filter' in case it already exists since NO_CREATE = true,
// otherwise it will send and ResponseException
$client->cuckooFilterInsert('test-filter', ['foo', 'bar', 18], $options);
```

**Returns:** (array) of true/false values, indicating if each item (in the position which was inserted) was added to 
the filter or an error happened.`ResponseException` if some of the items are not string or number or in case 
we specify `NO_CREATE` = true and the filter doesn't exists.

### `Insert if not exist`
Similar to `Insert` but just inserts the item if it does not exist previously.

`$redisBloomClient->cuckooFilterInsertIfNotExist(string $key, array $items, array $options = []);`

or

`$cuckooFilter->insertIfNotExist(array $items, array $options = []);`

**Params:**
- key: (string) filter name
- items: (array) of (string|number) scalar values
- options: (array) optional, if specified it can contain up to 3 params:
    * capacity: (int) if specified set the number of entries you intend to add to the filter, if the filter already exists this value will be ignored
    * noCreate: (bool) if specified and equel to true, prevents automatic filter creation if the filter does not exist. Instead, an error will be returned if the filter does not already exist

**Returns:** (array) of true/false values, indicating if each item (in the position which was inserted) was inserted to 
the filter or could not be because the item already exist.`ResponseException` if some of the items are not string or number or in case 
we specify `NO_CREATE` = true and the filter doesn't exists.

### `Exists`
Determines whether an item may exist in the Bloom Filter or not.

`$redisBloomClient->cuckooFilterExists(string $key, $item);`

or

`$cuckooFilter->exists($item);`

**Params:**
- key: (string) filter name
- item: (string|number) scalar value to add

**Returns:** (bool) true if the item may exist in the filter, false if either the item doesn't exist in the filter or 
the filter doesn't exist. `ResponseException` if item is not string or number

### `Count`
Returns the number of times an item may be in the filter. Because this is a probabilistic data structure, this may not 
necessarily be accurate..

`$redisBloomClient->cuckooFilterCount(string $key, $item);`

or

`$cuckooFilter->count($item);`

**Params:**
- key: (string) filter name
- item: (string|number) item to count

**Returns:** (int) number of times the item exists in the filter, 0 if the item doesn't exist in the filter and also if
 the key doesn't exist. `ResponseException` if item is not string or number.

### `Delete`
Deletes an item once from the filter. If the item exists only once, it will be removed from the filter. If the item was 
added multiple times, it will still be present. **Deleting elements that are not in the filter may delete a different 
item, resulting in false negatives!**

`$redisBloomClient->cuckooFilterDelete(string $key, $item);`

or

`$cuckooFilter->delete($item);`

**Params:**
- key: (string) filter name
- item: (string|number) item to delete

**Returns:** (bool) true if item was deleted or false if it was not possible because it doesn't exist. 
`ResponseException` if item is not string or number or key does not exist.
 
### `ScanDump`
It iterates through a filter returning a chunk of data in each iteration. The first time this command is called, 
the value of the `iterator` should be 0. This command will return a successive array of `[iterator, data]` until iterator = 0 
and data = '', `[0, '']` to indicate completion.

`$redisBloomClient->cuckooFilterScanDump(string $key, int $iterator);`

or

`$cuckooFilter->scanDump(int $iterator);`

**Params:**
- key: (string) filter name
- iterator: (int) iterator value

**Returns:** (array) An array of `[iterator, data]`. The Iterator is passed as input to the next invocation of `ScanDump`. 
If the iterator is 0, it means iteration has completed. The iterator-data pair should also be passed to 
`LoadChunk` when restoring the filter. It throws a `ResponseException` in case `key` doesn't exist

### `LoadChunk`
Restores a filter previously saved using `ScanDump`. This command will overwrite any bloom filter stored under `key`. 
Ensure that the bloom filter will not be modified between invocations.


`$redisBloomClient->cuckooFilterLoadChunk(string $key, int $iterator, $data);`

or

`$cuckooFilter->loadChunk(int $iterator, $data);`

**Params:**
- key: (string) filter name
- iterator: (int) iterator value
- data: data chunk as returned by `ScanDump`

**Returns:** (bool) true on success. It throws a `ResponseException` in case `key` doesn't exist

### `Copy`
Currently, this command is only available in `CuckooFilter` class, not in `RedisBloomClient`.

It copies all data stored in the key specified in the `CuckooFilter` class into `key` target, basically it combines 
one `scanDump` with a `loadChunk` on the fly in each iteration until all data are consumed from the CuckooFilter object 
`key` source and inserted in the target `key`. 

`$cuckooFilter->copy(string $targetFilter);`

**Params:**
- targetFilter: (string) destination filter name

**Returns:** (bool) true on success. It throws a `ResponseException` in case of target `key` doesn't exist or an error or 
a failure happens. In case of error, the command will try to delete the target `key` before throwing the exception.