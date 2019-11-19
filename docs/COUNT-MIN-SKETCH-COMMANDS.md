# `Mins-Sketch` Commands
You can execute Count-Min Sketch commands in two ways:

**With RedisBloomClient**

First, you need to create a RedisBloomClient from RedisBloomFactory, and then execute the command from the client. 
You need to specify the name of the filter, as key param, in each command. You can execute Count-Min Sketch commands on 
different filters (keys) using RedisBloomClient. All Count-Min Sketch commands signatures in RedisBloomClient are prefixed 
with `countMinSketch`, like `countMinSketchIncrementBy` or `countMinSketchQuery`.

```
$factory = new RedisBloomFactory();
$client = $factory->createClient();
$client->countMinSketchQuery('count-min-sketch-key', 'item1', 'item2');
```

**With CountMinSketch class**

You can create a CountMinSketch object by instantiating it from RedisBloomFactory and then execute all CountMinSketch 
commands over one key which is specified as param when you create the CountMinSketch object.

```
$factory = new RedisBloomFactory();
$countMinSketch = $factory->createCountMinSketch('count-min-sketch-key');
$countMinSketch->initByDim(10, 10);
```

Both RedisBloomClient and CountMinSketch object can be configured with a specific connection to Redis when they are created by providing
a configuration array to `RedisBloomFactory::createClient(array $config)` or 
`RedisBloomFactory::createCountMinSketch(string $filterName, array $config)`, even you can provide a configuration array to
RedisBloomFactory, `RedisBloomFactory(array $config)`, and all clients and CountMinSketch objects created by the factory 
will be using that configuration. Please take a look at `examples/factory.php` to know how to provide configuration options.

## Commands
You can find a detailed description of each command in [RedisBloom - Count-Min Sketch Command Documentation](https://oss.redislabs.com/redisbloom/CountMinSketch_Commands/).
Also, for a better understanding of how Count-Min Sketch structures works I found some useful links [here](https://towardsdatascience.com/big-data-with-sketchy-structures-part-1-the-count-min-sketch-b73fb3a33e2a) 
and [here](https://medium.com/@gopalkrushnapattanaik/understanding-count-min-sketch-8a10590fc936).

### `Init by dimension`
Initializes a Count-Min Sketch to dimensions by providing `width` and `depth`

`$redisBloomClient->countMinSketchInitByDim(string $key, int $width, int $depth);`

or

`$countMinSketch->initByDim(int $width, int $depth);`

**Params:**
- key: (string) sketch name
- width: (int) number of counters in each array. Reduces the error size. We could say that `width` represents the number of possible different values that all hash functions can return 
- depth: (int) number of counter-arrays. Reduces the probability for an error of a certain size (percentage of total count). `depth` would represent the number of different hash functions

**Returns:** (bool) true if the sketch was created. It throws a `ResponseException` if the sketch already exists or 
`width` and `depth` cannot be converted to integers.

### `Init by probability`
Initializes a Count-Min Sketch to accommodate requested capacity.

`$redisBloomClient->countMinSketchInitByProb(string $key, float $errorRate, float $probability);`

or

`$countMinSketch->initByProb(float $errorRate, float $probability);`

**Params:**
- key: (string) sketch name
- error: (float) estimate size of error. The error is a percent of total counted items (0.0 < error < 1.0) . This affects the width of the sketch
- probability: (float) desired probability for inflated count (0.0 < error < 1.0). This effects the depth of the sketch. For example, for a desired false positive rate of 0.1% (1 in 1000), error_rate should be set to 0.001. The closer this number is to zero, the greater the memory consumption per item and the more CPU usage per operation

**Returns:** (bool) true if the sketch was created to the filter, `ResponseException` if sketch key already exists 
or `error` or `probability` are out of range or they are not floats

### `Increment by`
Increases the count of one or more items.

`$redisBloomClient->countMinSketchIncrementBy(string $key, ...$itemsIncrease);`

or

`$countMinSketch->incrementBy(...$itemsIncrease);`

**Params:**
- key: (string) sketch name
- $itemsIncrease: comma-separated list of `ìtem` (string|number) followed by its `increment` (int), you can specify more 
then one pair of (`item`, `increment`)

** Example**
Increments the count of `item1` by 13, the integer item 34 by 17, the string item `34` by 13, the float 1.2 by 100 
and the string `1.2` by 200
`$redisBloomClient->countMinSketchIncrementBy('sketch-key', 'item1', 13, 34, 17, '34', 13, 1.2, 100, '1.2', 200);`

or  using CountMinSketch class 

`$countMinSketch->incrementBy(item1', 13, 34, 17, 1.2, 100, 13, 34, 17, '34', 13, 1.2, 100, '1.2', 200);`

At the end we will have:
- count for item `34`: 17 + 13 = 30
- count for item `34`: 100 + 200 = 300

**Returns:** (bool) true if items were incremented successfully.`ResponseException` if some of the items is not a string 
or number, `increment` is not an integer, `increment` is missing for the related `item` (so length of list of params, 
except sketch key, is not even) or sketch key doesn't exist.

### `Query`
Returns the count of items specified as params, we can query more than one item.

`$redisBloomClient->countMinSketchQuery(string $key, ...$items);`

or

`$countMinSketch->query(...$items);`

**Params:**
- key: (string) sketch name
- items: list of (string|number) scalar values representing the item names for querying

**Returns:** (array) of counts for each item specified in the parameter list in the same order they were specified. If 
one item specified as param doesn't exist in the sketch it will return a count of 0.`ResponseException` if some of the 
items are not string or sketch doesn't exist.

### `Merge`
Merges several sketches into one sketch. All sketches (sources and target) must be initialized and have identical 
width and depth.

`$redisBloomClient->countMinSketchMerge(string $destKey, int $numKeys, array $sketchKeys, array $weights = []);`

or you can merge existing sketches into CountMinSketch object

`$countMinSketch->mergeFrom(int $numKeys, array $sketchKeys, array $weights = []);`

**Params:**
- destKey: (string) target sketch name where other sketches will be merged
- numKeys: (int) number of source sketches where to merge from
- sketchKeys: (array) list of sketch names sources where to merge from
- weights: (array) optional, contains the integer values for multiplying the count of each item in the correspondent 
source sketch before merging

**Returns:** (bool) true on success.`ResponseException` is thrown in the following cases:
- `numKeys` is not equal to the length of `sketchKeys` array
- `weights` lengths is greater than `numKeys` or the length of `sketchKeys` array
- `weights` values are not integers
- `sketchKeys` are not initialized
- target sketch specified in `destKey` and source sketches includes in `sketchKeys` have different `width` and `depth`

You can find `merge` usage in [examples/count-min-sketch-merge.php](https://github.com/averias/phpredis-bloom/blob/master/examples/count-min-sketch-merge.php) file.

### `Info`
Returns width, depth and total count of the sketch.

`$redisBloomClient->countMinSketchInfo(string $key);`

or

`$countMinSketch->info();`

**Params:**
- key: (string) sketch name

**Returns:** (associative array) with the following structure:
```
[
   'width' => 10, // width dimension of the scketch
   'depth' => 10, // deepth dimension of the scketch
   'count' => 4582 // total count of all elements in the sketch (that is the sum of the count of all different item in the sketch)
]
```

It throws a`ResponseException` in case of sketch key doesn't exist.
