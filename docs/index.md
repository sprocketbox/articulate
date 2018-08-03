# What is Articulate?

Articulate is a data source agnostic entity mapper package for Laravel. It allows you to use simple data objects (entities) 
without having to deal with the nuances of an ORM, or worrying about where they came from.

# Why Articulate?

Articulate is designed to provide domain based entities that are separated from the data sources that populate them.
An entity can have as little or as much resemblance to its data source as you wish.

Your business and application logic won't care whether the data came from a static file, an array, a database or even an API.
All it cares about is the resource returned from that source, and Articulate aims to help there by providing a level of
abstraction.

# How does Articulate work?

Every [entity](entities/index.md) in Articulate has a [mapper](entities/mappers.md) that provides a 
[mapping](entities/mappings.md) between entity [attributes](attributes/index.md)
and data source columns. Each entity also belongs to a data [source](sources/index.md), dictating where the data should come from,
and also have a [respository](entities/repositories.md) that is used to interact with said data source. 