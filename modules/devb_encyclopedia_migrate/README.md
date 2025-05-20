# Nodegoat Migrations

## Description

This module migrates encyclopedia content from the NodeGoat source, to Drupal
encyclopedia nodes.

## NodeGoat API

The NodeGoat API is a REST API. There are API endpoints for each type of
encyclopedia content. Each type has an endpoint for the model (= data structure
definition) and the actual data.

### Authentication

Happens via a fixed "Authorization: Bearer [token]" header access token.
Put this token in a file located at private://keys/nodegoat_oauth2.key
The "Key" module is used to manage this key.

### Endpoints: models

* Persoon: https://api.onderzoek.advn.be/project/254/model/type/953
* Organisatie: https://api.onderzoek.advn.be/project/254/model/type/959
* Publicatie: https://api.onderzoek.advn.be/project/254/model/type/962
* Event: https://api.onderzoek.advn.be/project/254/model/type/965
* Artwork: https://api.onderzoek.advn.be/project/254/model/type/1064
* Monument: https://api.onderzoek.advn.be/project/254/model/type/967
* Concept: https://api.onderzoek.advn.be/project/254/model/type/987
* Location: https://api.onderzoek.advn.be/project/254/model/type/966
* Region: https://api.onderzoek.advn.be/project/254/model/type/1037

### Endpoints: data

* Persoon: https://api.onderzoek.advn.be/project/254/data/type/953/filter/10/object
* Organisatie: https://api.onderzoek.advn.be/project/254/data/type/959/filter/11/object
* Publicatie: https://api.onderzoek.advn.be/project/254/data/type/962/filter/12/object
* Event: https://api.onderzoek.advn.be/project/254/data/type/965/filter/13/object
* Artwork: https://api.onderzoek.advn.be/project/254/data/type/1064/filter/38/object
* Monument:  https://api.onderzoek.advn.be/project/254/data/type/967/filter/15/object
* Concept: https://api.onderzoek.advn.be/project/254/data/type/987/filter/16/object
* Location: https://api.onderzoek.advn.be/project/254/data/type/966/filter/14/object
* Region: https://api.onderzoek.advn.be/project/254/data/type/1037/filter/17/object

### Using NodeGoat model & data API endpoints
Each field in the model has a unique "object_description_id", which we use to
get the data for that particular field in the data API call.

Example from Publicatie model:
```
...
"3896": {
  "object_description_id": 3896,
  "object_description_name": "1.1 Titel",
  "object_description_value_type_base": "",
  "object_description_value_type_settings": [],
  "object_description_is_required": true,
  "object_description_is_unique": false,
  "object_description_is_identifier": false,
  "object_description_has_multi": false,
  "object_description_ref_type_id": false
},
...
```
This tells us that the "1.1 Titel" field is represented by ID 3896.

Example from Publicatie data:
```
...
"object_definitions": {
  ...
  "3896": {
    "object_description_id": 3896,
    "object_definition_ref_object_id": null,
    "object_definition_value": "De Schakel / El Lazo",
    "object_definition_sources": [],
    "object_definition_style": []
  },
  ...
},
...
```
We use the "object_definition_value" property to get the field data. We use the
mapped ID 3896 to get the data for the "1.1 Titel" field.

### Migration logic

For simple fields in NodeGoat, we can retrieve the field value via the xpath
`object_definitions/[object_description_id]/object_definition_value`.
For complex fields (with multiple properties per field item), we can use the
`object_subs` source property, as defined in
`\Drupal\devb_encyclopedia_migrate\Plugin\migrate\source\NodeGoatUrl`.
