# RAG (Retrieval-Augmented Generation) — guide

Ce document décrit comment organiser la documentation qui servira à la RAG.

- Emplacement recommandé: `docs/<api-slug>/` ou `Pages_<slug>/docs/`.
- Format: fichiers Markdown (`.md`) décrivant endpoints, exemples, et contenu indexable.
- Indexation: pour une RAG simple, charger les fichiers Markdown et faire une recherche textuelle; pour une RAG avancée, produire des vecteurs (external service).

Champs recommandés pour chaque doc:
- `title`, `description`, `tags`, `source`, `created_at`.
