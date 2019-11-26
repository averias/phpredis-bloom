# `Top-K` Commands
You can execute Top-K commands in two ways:

**With RedisBloomClient**

First, you need to create a RedisBloomClient from RedisBloomFactory, and then execute the command from the client. 
You have to specify the name of the filter, as key param, in each command. You can execute Top-K commands on 
different keys using RedisBloomClient. All Top-K commands signatures in RedisBloomClient are prefixed 
with `topK`, like `topkList` or `topKQuery`.

```
$factory = new RedisBloomFactory();
$client = $factory->createClient();
$client->topKQuery('top-k-key', 'item1', 'item2');
```

**With TopK class**

You can create TopK objects by instantiating them from RedisBloomFactory and then execute all TopK commands over one 
key which is specified as param when you create the TopK object.

```
$factory = new RedisBloomFactory();
$topK = $factory->createTopK('top-k-key');
$topK->reserve(4, 300, 10, 0.95);
```

Both RedisBloomClient and TopK object can be configured with a specific connection to Redis when they are created by providing
a configuration array to `RedisBloomFactory::createClient(array $config)` or 
`RedisBloomFactory::createTopK(string $filterName, array $config)`, even you can provide a configuration array to
RedisBloomFactory, `RedisBloomFactory(array $config)`, and all clients and TopK objects created by the factory 
will be using that configuration. Please take a look at `examples/factory.php` to know how to provide configuration options.

## Commands
You can find a detailed description of each command in [RedisBloom - Top-K Command Documentation](https://oss.redislabs.com/redisbloom/TopK_Commands/).
Also, for a better understanding of how Top-K structures works, you can take a read to 
[HeavyKeeper: An Accurate Algorithm for Finding Top-k Elephant Flows](https://www.usenix.org/system/files/conference/atc18/atc18-gong.pdf).

### `Reserve`
Initializes a Top-K`

`$redisBloomClient->topKReserve(string $key, int $topK, int $width, int $depth, float $decay);`

or

`$topK->reserve(int $topK, int $width, int $depth, float $decay);`

**Params:**
- key: (string) top-k name
- topK: (int) number of top counted item to keep
- width: (int) number of counters kept in each array 
- depth: (int) number of counter-arrays
- decay: (float) The probability of reducing a counter in an occupied bucket. Therefore, as the counter gets higher, the chance of a reduction is being reduced. Its value must be > 0.0 and <= 1.0


**Returns:** (bool) true if the top-k was created. It throws a `ResponseException` if the top-k key already exists,  
`k`, `width` or `depth` cannot be converted to integers or `decay` cannot be converted to float and is <= 0 and > 1.

### `Add`
Adds an item to the data structure. Multiple items can be added at once. If an item enters the top-k list, the item 
which is expelled is returned. This allows dynamic heavy-hitter detection of items being entered or expelled from Top-K 
list.

If one item, `x`, enters the top-k list, and some elements which are already in the top-k list have the same value that 
`x`, `x` will be inserted before those that have the same value than it

`$redisBloomClient->topKAdd(string $key, ...$items);`

or

`$topK->add(...$items);`

**Params:**
- key: (string) top-k name
- items: comma-separated list of `ìtems` (string|number) to add

**Returns:** (array) for every position in the array matching the position in the list of items passed as params, 
it indicates:
- false: if adding the item didn't expel any existing item from the top-k list
- item **as string**: item that was expelled by adding the new one

It is important to note that all the expelled items **will be always returned as strings** even if you inserted them, 
at some point, like integers or floats. In the same way, `add` or `incrementBy` one integer and its string representation 
has the same effect, so `add` the integer 12 and the string `12` will count as 2 same items. Same behavior for floats, 
float 37.5 and the string `37.5` are the same item.

It throws and `ResponseException` if top-k key already exists or items are not string or numeric

### `Increment by`
Increase the score of an item in the data structure by increment. Multiple items' score can be increased at once. 
If an item enters the Top-K list, the item which is expelled is returned.

`$redisBloomClient->topKIncrementBy(string $key, ...$itemsIncrease);`

or

`$topK->incrementBy(...$itemsIncrease);`

**Params:**
- key: (string) sketch name
- $itemsIncrease: comma-separated list of `ìtem` (string|number) followed by its `increment` (int), you can specify more 
then one pair of (`item`, `increment`)

**Example**

Increments the count of `item1` by 13, the integer item 34 by 17, the string item `34` by 13, the float 1.2 by 100 
and the string `1.2` by 200
`$redisBloomClient->topKIncrementBy('top-k-key', 'item1', 13, 34, 17, '34', 13, 1.2, 100, '1.2', 200);`

or  using CountMinSketch class 

`$topk->incrementBy(item1', 13, 34, 17, 1.2, 100, 13, 34, 17, '34', 13, 1.2, 100, '1.2', 200);`

At the end we will have:
- count for item `34`: 17 + 13 = 30
- count for item `34`: 100 + 200 = 300

**Returns:** (array) similar to `add` response, for every position in the array matching the position in the list of 
items passed as params, it indicates:
- false: if incrementing the item didn't expel any existing item from the top-k list
- item **as string**:  the item that was expelled by adding the new one, like `add` command, all the expelled items **will be always returned as strings** regardless of they were incremented as float or integers

It throws a `ResponseException` if some of the items is not a string or number, `increment` is not an integer, 
`increment` is missing for the related `item` (so the length of the list of params, except top-k key, is not even) or top-k 
key doesn't exist.

### `Query`
Checks whether an item is one of Top-K items. Multiple items can be checked at once.

`$redisBloomClient->topKQuery(string $key, ...$items);`

or

`$topK->query(...$items);`

**Params:**
- key: (string) top-k name
- items: list of (string|number) scalar values representing the item names for querying

**Returns:** (array) of booleans for each item specified in the parameter list in the same order they were specified. If 
one item specified as param isn't in the top-k list the value will be false, otherwise true.`ResponseException` if some of the 
items are not string or top-k key doesn't exist.

### `Count`
Returns count for an item. Please note this number will never be higher than the real count and likely to be lower. 
Multiple items can be added at once.

`$redisBloomClient->topKCount(string $key, ...$items);`

or you can merge existing sketches into CountMinSketch object

`$topK->count(...$items);`

**Params:**
- key: (string) 
- items: list of (string|number) scalar values representing the item names for querying

**Returns:** (array) of integers for each item specified in the parameter list in the same order they were specified and  
indicating the number of occurrences in the top-k structure. If the item doesn't exist in the structure it will return 
0. `ResponseException` if some of the items are not string or numeric, or top-k key doesn't exist.

### `List`
Return full list of items in Top K list.

`$redisBloomClient->topKList(string $key);`

or

`$topK->list();`

**Params:**
- key: (string) top-k name

**Returns:** (array) of item names as strings for each item specified in the parameter list in the same order they were 
specified. The size of this array will <= `k` (see `reserve` or `info` commands) and only will include the top-k items
 in the structure, i.e. if we have a top-k structure with `k` = 5 and we only have 3 items in the structure, the returned 
 array will contain just 3 elements.
  
It throws a `ResponseException` if some of the items are not string or numeric, or top-k key doesn't exist.

### `Info`
Returns top-k list size (`k`), `width`, `depth` and `decay`.

`$redisBloomClient->topKInfo(string $key);`

or

`$topK->info();`

**Params:**
- key: (string) top-k name

**Returns:** (associative array) with the following structure:
```
[
   'k' => 3, // size of the top-k list
   'width' => 100, // width dimension of the top-k structure
   'depth' => 7, // deepth dimension of the top-k structure
   'decay' => 0.98 // decay value
]
```

It throws a`ResponseException` in case of top-k key doesn't exist.
