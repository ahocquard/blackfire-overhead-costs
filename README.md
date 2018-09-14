# Goal

Demonstrate at which point the Blackfire adds an overhead cost, leading to incorrect results in the graph.

This is an example coming from the real life in our application (Akeneo PIM). I have just simplified the code to better understand the problem. 

## Context

Akeneo PIM manages products.
A product contains several properties, called product values, such as the color, the size, etc.

For some customers, a product can contain several hundred product values, or even several thousands.

When persisting this collection of values (Mysql, Elasticsearch), we use intensively the normalizer pattern from the Symfony component, to transform objects as array.

## Problems

Do note that, in theory these problems should be highlited by the use of the profiler.
It's the role of the profile to determine if an optimization worths an improvement or not.

Here, we are doing the opposite: it is to better understand the problem in a Blackfire profile.

### Normalizer pattern

The normalizer pattern iterates over all normalizers declared in the serializer in order to find the good one for a given object (`supports` function).
The more you have normalizers in the registry, slower it is to find the good normalizer.

So, when normalizing a value collection of 1000 products values and having 15 normalizers registered in the serializer, a lot of time is wasted at finding the normalizer for the product values. 

Note: The complexity  of looping on it is O(m * n) where "m" is the number of product values and "n" the number of normalizers declared in the registry.

The solution to avoid this complexity is to call directly the normalizer instead of calling the serializer.

### array_merge_recursive in a loop

Each value is merged into the normalized value collection by using `array_merge_recursive`.
The problem is pretty obvious when you know how this function works.

The `$result` variable has one more item per iteration in the loop.
As the arrays to be merged are passed by copy, it copies an array which becomes bigger and bigger.

Note: we can easily proove that it's a O(m^2) complexity as the complexity is a suite 1 + 2 + 3 + ... + "m", where "m" is the number of product values.

As `array_merge_recursive` is a variadic function, the solution is to call only once the function this way : `array_merge_recusrive(...$normalizedValues)`.

## Results

I did the benchmark on my computer, a macbook air, with PHP 7.1.21.
I normalized 100 products with 500 product values. 15 normalizers are registered in the serializer registry.
I use microtime to know the real time passed in the normalizion and in the merge, in order to compare with Blackfire profile.

### Without optimization

```
$ php test_without_optimization.php
```

- total time: 422 ms
- normalization: 150 ms - 35%
- array_merge_recursive: 250 ms - 59%

```
$ blackfire run php test_without_optimization.php
```

- total time: 5472 ms 
- normalization: 4496 ms - 82%
- array_merge_recursive: 625 ms - 11%

[Profile](https://blackfire.io/profiles/9d9f444b-4c29-4274-bfd3-0a9ae145e26c/graph)

### Normalization optimization

```
$ php test_optimized_normalization.php
```

- total time: 320 ms 
- normalization: 34 ms - 10%
- array_merge_recursive: 262 ms - 81%

Improvement: 24%

```
$ blackfire run php test_optimized_normalization.php
```

- total time: 1905 ms 
- normalization: 992 ms - 52%
- array_merge_recursive: 583 ms - 30%

[Profile](https://blackfire.io/profiles/63ff0b4c-0de1-4bec-b6bc-df81df688e33/graph)

### array_merge_recursive optimization

```
$ php test_optimized_merge.php
```

- total time: 163 ms
- normalization: 146 ms - 89%
- array_merge_recursive: 3 ms - 2%

Improvement: 61%

```
$ blackfire run php test_optimized_merge.php
```

- total time: 4577 ms 
- normalization: 4382 ms - 96%
- array_merge_recursive: 8 ms - 0%

[Profile](https://blackfire.io/profiles/1435db77-1d66-4fd1-a6cc-d7aba800a4e7/graph)
