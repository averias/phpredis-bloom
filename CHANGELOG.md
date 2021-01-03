## 1.2.2 - 2021-01-03
- Fixed bug in namespace for BaseTestIntegration class

## 1.2.1 - 2020-01-30
- Added code highlights in docs and fixed some typos in examples

## 1.2.0 - 2020-01-28
- Changes compatible with RedisLabs RedisBloom v 2.2.1
- NON_SCALING and EXPANSION combination is not allowed in Bloom Filter Reserve and Insert commands 

## 1.1.0 - 2019-12-29
- Updated documentation for Bloom and Cuckoo filters
- Moved test coverage from scrutinizer to code climate

## 1.0.0 - 2019-12-03
- Changes compatible with RedisLabs RedisBloom v 2.2.0
- Counter-Min Sketch IncrementBy command returns now a array of counters for each incremented item, before returned a boolean
- Added NON_SCALING optional param to Bloom Filter Reserve and Insert commands

## 0.5.0 - 2019-12-01
- Added CHANGELOG.md

## 0.4.0 - 2019-11-26
- Added EXPANSION option to Bloom Filter Reserve and Insert commands

## 0.3.0 - 2019-11-26

- Small refactoring
- Travis and Scrutinizer config files

## 0.2.0 - 2019-11-21

- BF Insert command doesn't accept now error rate = 1, it has to be < 1
- BF and CF Info commands implementation, tests and documentation

## 0.1.2 - 2019-11-19

- More changes in documentation

## 0.1.1 - 2019-11-18

- Small changes in documentation

## 0.1.0 - 2019-11-18

- First implementation compatible with RedisLabs RedisBloom v 2.0.x
