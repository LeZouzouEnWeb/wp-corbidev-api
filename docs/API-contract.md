# API contract

Ce document décrit le format minimal pour décrire des endpoints utilisables par `Api\Registrar`.

Chaque endpoint attend:
- `method` (GET|POST|PUT|DELETE)
- `route` (ex: `/items`)
- `callback` (nom de la fonction/closure ou clé pour mapping)
- `permission` (capability ou `__return_true`)

Format recommandé (JSON):

```
{
  "endpoints": [
    {"method":"GET","route":"/info","callback":"info_callback","permission":"__return_true"}
  ]
}
```
