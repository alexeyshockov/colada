# TODO

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/pyrech/composer-changelogs/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/pyrech/composer-changelogs/?branch=master)

[![Code Coverage](https://scrutinizer-ci.com/g/alexeyshockov/colada/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/alexeyshockov/colada/?branch=master)

## Design

Public API is mostly _final_. Non final classes can be extended. Also traits are available.

About final classes: http://ocramius.github.io/blog/when-to-declare-classes-final/

### Error handling

- For simple type control you should use exception. Otherwise most of your code will return Try.
  - Example: if not traversable — throw exception
- Options should be used when value may be not present.
  - Example: Iterable::find(callable), Iterable::headOption() 
- Try monad should be used, when it's normal to have success of failure.
  - Example: DB::commit()



## Matcher

- check that IterableOnce and empty in the end
- count callable invocation count: map(), filter(), toArray()...



## Traversable only

     "DatePeriod",
     "DOMNodeList",
     "DOMNamedNodeMap",
     "mysqli_result",
     "PDOStatement",
     "SimpleXMLElement",
     "ResourceBundle",
     "IntlBreakIterator",
     "IntlRuleBasedBreakIterator",
     "IntlCodePointBreakIterator",
