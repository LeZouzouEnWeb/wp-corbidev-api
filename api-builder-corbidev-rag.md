# RAG – API Builder declaratif & versionne sous WordPress

Projet : API CV / API Builder CorbiDev
Auteur : Eric Corbisier

## Vision

API builder dynamique, versionne, securise, admin WordPress.

## Concepts

Model, Version, Module, Field, Policy, Runtime.

## Tables

wp_api_models

- id
- slug
- name
- description
- active_version_id
- created_at

wp_api_model_versions

- id
- model_id
- version
- status
- expires_at
- manifest_json
- created_at

wp_api_runtime_data

- id
- model_id
- version_id
- module
- data_json
- created_at
- updated_at

wp_api_keys

- id
- key_hash
- permissions_json
- active
- created_at

## Regles

- Version active immuable
- Toute modification cree une nouvelle version
- Version obsolete avec date de fin
- Suppression uniquement si expiree

## Reponse API

status, title, message, data, version, deprecated, expires_at
