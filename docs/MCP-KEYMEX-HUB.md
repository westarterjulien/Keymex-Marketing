# MCP Server : keymex-hub (Brain Memory Claude)

Documentation complete du serveur MCP utilise pour la gestion de projets, la memoire de code et le deploiement Dokploy.

---

## Table des matieres

1. [Introduction](#introduction)
2. [Configuration](#configuration)
3. [Gestion de Projet](#gestion-de-projet)
4. [Decisions et Architecture](#decisions-et-architecture)
5. [Taches (Todos)](#taches-todos)
6. [Bugs](#bugs)
7. [Specifications](#specifications)
8. [Conventions de Code](#conventions-de-code)
9. [Design Assets et Tokens](#design-assets-et-tokens)
10. [Agents et Recommandations](#agents-et-recommandations)
11. [Journal de Developpement](#journal-de-developpement)
12. [Memoire de Code](#memoire-de-code)
13. [Sessions de Developpement](#sessions-de-developpement)
14. [Snippets](#snippets)
15. [Regles de Qualite](#regles-de-qualite)
16. [Contrats API](#contrats-api)
17. [Commandes Rapides](#commandes-rapides)
18. [Contexte et Restauration](#contexte-et-restauration)
19. [Integration Dokploy](#integration-dokploy)

---

## Introduction

Le serveur MCP **keymex-hub** (alias **brain-memory-claude**) est un outil de productivite pour Claude Code qui permet de :

- Maintenir une memoire persistante entre les sessions
- Gerer les projets, decisions, conventions et specifications
- Indexer et comprendre le codebase
- Integrer avec Dokploy pour le deploiement
- Recommander les agents optimaux selon les taches

### Principe de fonctionnement

```
+------------------+     +------------------+     +------------------+
|   Claude Code    | --> |   MCP Server     | --> |   SQLite DB      |
|   (Client)       |     |   (keymex-hub)   |     |   (Persistance)  |
+------------------+     +------------------+     +------------------+
```

---

## Configuration

### Installation

Le serveur MCP est configure dans `~/.claude/claude_desktop_config.json` ou equivalent.

### Utilisation dans CLAUDE.md

```markdown
# Instructions Projet

**MCP** : `keymex-hub`

## AUTOMATISMES MCP

### Au DEBUT de chaque session
mcp__keymex-hub__get_restore_context({ path: "/chemin/projet", depth: "full" })

### A la FIN de chaque session
mcp__keymex-hub__save_conversation_summary({ path: "/chemin/projet", summary: "..." })
```

---

## Gestion de Projet

### register_project

Enregistre ou met a jour un projet.

```javascript
mcp__keymex-hub__register_project({
  path: "/home/user/mon-projet",      // Chemin absolu (requis)
  name: "Mon Projet",                  // Nom du projet (requis)
  description: "Description du projet" // Optionnel
})
```

### list_projects

Liste tous les projets enregistres.

```javascript
mcp__keymex-hub__list_projects({})
```

### get_project_context

Recupere le contexte complet d'un projet (infos, decisions, todos, bugs, specs, conventions).

```javascript
mcp__keymex-hub__get_project_context({
  path: "/home/user/mon-projet"
})
```

### scan_project_structure

Scanne et enregistre la structure de fichiers d'un projet.

```javascript
mcp__keymex-hub__scan_project_structure({
  path: "/home/user/mon-projet",
  ignore: ["*.log", "tmp/"]  // Patterns a ignorer (optionnel)
})
```

### search_files

Recherche des fichiers dans la structure enregistree.

```javascript
mcp__keymex-hub__search_files({
  path: "/home/user/mon-projet",
  pattern: "Controller"  // Ex: "User", ".vue", "service"
})
```

### get_project_stats

Obtient les statistiques d'un projet.

```javascript
mcp__keymex-hub__get_project_stats({
  path: "/home/user/mon-projet"
})
```

### init_project_workflow

Initialise le workflow complet d'un projet (regles d'agents, scan, import specs, CLAUDE.md).

```javascript
mcp__keymex-hub__init_project_workflow({
  path: "/home/user/mon-projet",
  name: "Mon Projet",
  description: "Description",
  spec_file: "/chemin/vers/cahier-des-charges.md"  // Optionnel
})
```

---

## Decisions et Architecture

### add_decision

Ajoute une decision architecturale.

```javascript
mcp__keymex-hub__add_decision({
  path: "/home/user/mon-projet",
  title: "Utiliser Repository Pattern",
  description: "Separation de la logique metier et acces donnees",
  rationale: "Facilite les tests et la maintenabilite"
})
```

### list_decisions

Liste les decisions d'un projet.

```javascript
mcp__keymex-hub__list_decisions({
  path: "/home/user/mon-projet",
  active_only: true  // Optionnel, defaut: false
})
```

---

## Taches (Todos)

### add_todo

Ajoute une tache au projet.

```javascript
mcp__keymex-hub__add_todo({
  path: "/home/user/mon-projet",
  title: "Implementer authentification OAuth",
  description: "Ajouter login via Google et Microsoft",
  priority: "high",      // low, medium, high, critical
  category: "feature"    // feature, bugfix, refactor, docs, etc.
})
```

### list_todos

Liste les taches d'un projet.

```javascript
mcp__keymex-hub__list_todos({
  path: "/home/user/mon-projet",
  pending_only: true  // Optionnel
})
```

### update_todo_status

Met a jour le statut d'une tache.

```javascript
mcp__keymex-hub__update_todo_status({
  todo_id: 42,
  status: "done"  // pending, in_progress, done, cancelled
})
```

---

## Bugs

### add_bug

Ajoute un bug connu au projet.

```javascript
mcp__keymex-hub__add_bug({
  path: "/home/user/mon-projet",
  title: "Erreur 500 sur login",
  description: "L'authentification echoue quand...",
  severity: "high",           // low, medium, high, critical
  file_path: "app/Http/Controllers/AuthController.php"
})
```

### list_bugs

Liste les bugs d'un projet.

```javascript
mcp__keymex-hub__list_bugs({
  path: "/home/user/mon-projet",
  open_only: true  // Optionnel
})
```

### update_bug_status

Met a jour le statut d'un bug.

```javascript
mcp__keymex-hub__update_bug_status({
  bug_id: 15,
  status: "resolved"  // open, in_progress, resolved, wontfix
})
```

---

## Specifications

### add_spec

Ajoute une specification/cahier des charges.

```javascript
mcp__keymex-hub__add_spec({
  path: "/home/user/mon-projet",
  title: "Module de facturation",
  content: "## Fonctionnalites\n- Generation PDF\n- Envoi par email",
  category: "functional"  // functional, technical, ux, api, etc.
})
```

### list_specs

Liste les specifications d'un projet.

```javascript
mcp__keymex-hub__list_specs({
  path: "/home/user/mon-projet",
  category: "functional"  // Optionnel, filtrer par categorie
})
```

### get_spec

Recupere le contenu complet d'une specification.

```javascript
mcp__keymex-hub__get_spec({
  spec_id: 35
})
```

### import_spec_file

Importe un fichier markdown comme specification avec extraction automatique.

```javascript
mcp__keymex-hub__import_spec_file({
  path: "/home/user/mon-projet",
  file_path: "/chemin/vers/spec.md",
  category: "functional",
  extract_conventions: true,   // Extraire les conventions
  extract_decisions: true,     // Extraire les decisions
  extract_todos: true,         // Extraire les todos
  split_sections: false        // Decouper par section H1/H2
})
```

---

## Conventions de Code

### add_convention

Ajoute une convention de code.

```javascript
mcp__keymex-hub__add_convention({
  path: "/home/user/mon-projet",
  category: "naming",     // naming, structure, patterns, etc.
  rule: "Les composants Livewire sont en PascalCase",
  example: "UserProfile, OrderList, DashboardStats"
})
```

### list_conventions

Liste les conventions d'un projet.

```javascript
mcp__keymex-hub__list_conventions({
  path: "/home/user/mon-projet",
  category: "naming"  // Optionnel
})
```

---

## Design Assets et Tokens

### add_design_asset

Ajoute un asset design (screenshot, maquette, logo, etc.).

```javascript
mcp__keymex-hub__add_design_asset({
  path: "/home/user/mon-projet",
  type: "mockup",          // screenshot, mockup, logo, icon, wireframe, other
  title: "Maquette Dashboard",
  description: "Design Figma du dashboard principal",
  file_path: "/chemin/local/mockup.png",  // Ou url: "https://..."
  tags: "dashboard,admin,ui"
})
```

### list_design_assets

Liste les assets design d'un projet.

```javascript
mcp__keymex-hub__list_design_assets({
  path: "/home/user/mon-projet",
  type: "mockup"  // Optionnel
})
```

### add_design_token

Ajoute un token de design (charte graphique).

```javascript
mcp__keymex-hub__add_design_token({
  path: "/home/user/mon-projet",
  category: "colors",      // colors, typography, spacing, shadows, borders, components
  name: "primary",
  value: "#8B5CF6",
  css_variable: "--color-primary",
  description: "Couleur principale de la marque"
})
```

### list_design_tokens

Liste les tokens de design.

```javascript
mcp__keymex-hub__list_design_tokens({
  path: "/home/user/mon-projet",
  category: "colors"  // Optionnel
})
```

### get_design_css

Genere le CSS des design tokens (variables CSS).

```javascript
mcp__keymex-hub__get_design_css({
  path: "/home/user/mon-projet"
})
```

---

## Agents et Recommandations

### get_recommended_agent

**OBLIGATOIRE avant chaque tache.** Recommande l'agent optimal selon la description.

```javascript
mcp__keymex-hub__get_recommended_agent({
  path: "/home/user/mon-projet",
  task_description: "Creer un composant Livewire pour le formulaire de contact"
})

// Retourne: { agent: "laravel-12-livewire-developer", confidence: 95 }
```

### add_agent_rule

Ajoute une regle de recommandation d'agent personnalisee.

```javascript
mcp__keymex-hub__add_agent_rule({
  path: "/home/user/mon-projet",
  pattern: "api|endpoint|rest",
  agent_type: "api-developer",
  description: "Developpement d'APIs REST",
  priority: 80
})
```

### list_agent_rules

Liste les regles de recommandation d'agents.

```javascript
mcp__keymex-hub__list_agent_rules({
  path: "/home/user/mon-projet"
})
```

### Agents disponibles par defaut

| Agent | Pattern | Usage |
|-------|---------|-------|
| `laravel-12-livewire-developer` | laravel, livewire, eloquent | Dev Laravel/Livewire |
| `laravel-livewire-frontend-dev` | tailwind, css, ui, dark mode | Frontend Tailwind |
| `Explore` | find, search, explore, understand | Exploration codebase |
| `Plan` | plan, design, architect, feature | Planification |
| `claude-code-guide` | claude code, mcp, hook | Doc Claude Code |
| `Dokploy` | dokploy, deploy, docker | Deploiement |

---

## Journal de Developpement

### log_development_activity

**OBLIGATOIRE apres chaque developpement significatif.** Enregistre l'activite.

```javascript
mcp__keymex-hub__log_development_activity({
  path: "/home/user/mon-projet",
  activity_type: "feature",  // feature, bugfix, refactor, config, docs, test
  summary: "Implementation du systeme d'authentification OAuth",
  files_modified: ["app/Http/Controllers/AuthController.php", "routes/web.php"],
  decisions_made: ["Utiliser Laravel Socialite", "Stocker tokens en session"],
  agent_used: "laravel-12-livewire-developer"
})
```

### list_development_logs

Liste les activites de developpement recentes.

```javascript
mcp__keymex-hub__list_development_logs({
  path: "/home/user/mon-projet",
  limit: 20  // Optionnel, defaut: 20
})
```

---

## Memoire de Code

### scan_codebase

Scanne le codebase complet pour indexer fichiers, classes, fonctions, imports.

```javascript
mcp__keymex-hub__scan_codebase({
  path: "/home/user/mon-projet",
  exclude: ["tests/", "*.min.js"]  // Optionnel
})
```

### get_codebase_overview

Vue d'ensemble du codebase : fichiers par type, entry points, fichiers cles.

```javascript
mcp__keymex-hub__get_codebase_overview({
  path: "/home/user/mon-projet"
})
```

### get_file_dependencies

Recupere les dependances d'un fichier (imports, exports).

```javascript
mcp__keymex-hub__get_file_dependencies({
  path: "/home/user/mon-projet",
  file_path: "app/Services/MongoPropertyService.php"
})
```

### search_code_elements

Recherche des elements de code par nom.

```javascript
mcp__keymex-hub__search_code_elements({
  path: "/home/user/mon-projet",
  query: "Controller"
})
```

### index_file

Indexe un fichier dans la memoire de code.

```javascript
mcp__keymex-hub__index_file({
  project_id: 10,
  file_path: "app/Models/User.php",
  content_hash: "sha256...",
  file_type: "php",
  size_bytes: 2048
})
```

### add_code_element

Ajoute un element de code (classe, interface, fonction).

```javascript
mcp__keymex-hub__add_code_element({
  project_id: 10,
  file_id: 123,
  element_type: "class",  // class, interface, trait, function, constant, enum
  name: "UserService",
  namespace: "App\\Services",
  extends_from: "BaseService",
  implements_list: "UserServiceInterface",
  line_start: 15,
  line_end: 200
})
```

### add_code_pattern

Enregistre un pattern de code detecte.

```javascript
mcp__keymex-hub__add_code_pattern({
  project_id: 10,
  pattern_type: "repository",  // repository, service, factory, etc.
  name: "Repository Pattern",
  description: "Separation donnees/metier",
  example_file: "app/Repositories/UserRepository.php",
  usage_guidelines: "Utiliser pour tout acces BDD"
})
```

### save_code_knowledge

Sauvegarde une connaissance apprise sur le code.

```javascript
mcp__keymex-hub__save_code_knowledge({
  project_id: 10,
  knowledge_type: "gotcha",  // architecture, pattern, convention, gotcha, optimization, dependency
  title: "MongoDB dates en UTC",
  content: "Les dates MongoDB sont stockees en UTC, penser a convertir...",
  importance: "high",
  tags: "mongodb,dates,timezone",
  related_files: '["app/Services/MongoPropertyService.php"]'
})
```

### search_code_knowledge

Recherche dans les connaissances du projet.

```javascript
mcp__keymex-hub__search_code_knowledge({
  project_id: 10,
  query: "mongodb",
  knowledge_type: "gotcha"  // Optionnel
})
```

### get_code_context

Recupere le contexte complet du code pour demarrer une session.

```javascript
mcp__keymex-hub__get_code_context({
  project_id: 10,
  focus_area: "Controllers"  // Optionnel
})
```

---

## Sessions de Developpement

### start_dev_session

Demarre une nouvelle session de developpement.

```javascript
mcp__keymex-hub__start_dev_session({
  project_id: 10,
  goals: [
    "Implementer feature X",
    "Corriger bug Y"
  ]
})

// Retourne: { session_id: "uuid-xxx-yyy" }
```

### get_active_session

Recupere la session active d'un projet.

```javascript
mcp__keymex-hub__get_active_session({
  project_id: 10
})
```

### add_session_achievement

Ajoute un accomplissement a la session en cours.

```javascript
mcp__keymex-hub__add_session_achievement({
  session_id: "uuid-xxx-yyy",
  achievement: "Implementation OAuth terminee",
  files: ["app/Http/Controllers/AuthController.php"]
})
```

### end_dev_session

Termine une session de developpement.

```javascript
mcp__keymex-hub__end_dev_session({
  session_id: "uuid-xxx-yyy",
  summary: "Termine implementation OAuth et tests",
  achievements: ["OAuth Google", "OAuth Microsoft", "Tests unitaires"],
  pending_tasks: ["Documentation API"],
  notes: "Penser a ajouter refresh token"
})
```

### get_continuation_context

Recupere le contexte pour reprendre le travail.

```javascript
mcp__keymex-hub__get_continuation_context({
  project_id: 10
})
```

---

## Snippets

### create_snippet

Cree un snippet de code reutilisable avec placeholders.

```javascript
mcp__keymex-hub__create_snippet({
  name: "livewire-component",
  language: "php",
  category: "laravel",
  description: "Composant Livewire de base",
  content: `<?php

namespace App\\Livewire\\{{module}};

use Livewire\\Component;

class {{name}} extends Component
{
    public function render()
    {
        return view('livewire.{{module_lower}}.{{name_lower}}');
    }
}`,
  placeholders: '{"module": "Nom du module", "name": "Nom du composant"}',
  project_id: 10  // Optionnel, null = global
})
```

### get_snippet

Recupere un snippet par son nom.

```javascript
mcp__keymex-hub__get_snippet({
  name: "livewire-component",
  project_id: 10  // Optionnel
})
```

### render_snippet

Rend un snippet en remplacant les placeholders.

```javascript
mcp__keymex-hub__render_snippet({
  snippet_id: 5,
  variables: {
    module: "Dashboard",
    name: "Stats",
    module_lower: "dashboard",
    name_lower: "stats"
  }
})
```

### search_snippets

Recherche des snippets.

```javascript
mcp__keymex-hub__search_snippets({
  query: "livewire",
  language: "php",
  category: "laravel",
  project_id: 10
})
```

---

## Regles de Qualite

### add_quality_rule

Ajoute une regle de qualite de code.

```javascript
mcp__keymex-hub__add_quality_rule({
  category: "security",  // naming, structure, security, performance, style
  rule_name: "no-raw-sql",
  description: "Eviter les requetes SQL brutes",
  pattern: "DB::raw|->whereRaw",
  severity: "error",  // error, warning, info
  suggestion: "Utiliser Eloquent ou Query Builder",
  project_id: 10  // Optionnel
})
```

### list_quality_rules

Liste les regles de qualite.

```javascript
mcp__keymex-hub__list_quality_rules({
  project_id: 10,
  category: "security"  // Optionnel
})
```

### report_quality_issue

Signale un probleme de qualite de code.

```javascript
mcp__keymex-hub__report_quality_issue({
  project_id: 10,
  rule_id: 3,
  file_path: "app/Models/User.php",
  line_number: 45,
  code_excerpt: "DB::raw('SELECT * FROM users')"
})
```

### list_quality_issues

Liste les problemes de qualite d'un projet.

```javascript
mcp__keymex-hub__list_quality_issues({
  project_id: 10,
  status: "open",     // open, fixed, ignored
  category: "security"
})
```

### mark_quality_issue_fixed

Marque un probleme comme resolu.

```javascript
mcp__keymex-hub__mark_quality_issue_fixed({
  issue_id: 12
})
```

### get_quality_report

Genere un rapport de qualite du projet.

```javascript
mcp__keymex-hub__get_quality_report({
  project_id: 10,
  include_fixed: false
})
```

---

## Contrats API

### create_api_contract

Cree un contrat d'API pour documenter les endpoints.

```javascript
mcp__keymex-hub__create_api_contract({
  project_id: 10,
  name: "API Marketing",
  description: "API REST pour l'application marketing",
  version: "1.0.0",
  base_url: "/api/v1",
  auth_type: "bearer"  // none, bearer, api_key, basic, oauth2, custom
})
```

### add_api_endpoint

Ajoute un endpoint a un contrat d'API.

```javascript
mcp__keymex-hub__add_api_endpoint({
  contract_id: 1,
  method: "POST",  // GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD
  path: "/users/{id}",
  description: "Met a jour un utilisateur",
  params: '{"id": {"type": "integer", "required": true}}',
  headers: '{"Authorization": "Bearer {token}"}',
  request_body: '{"name": "string", "email": "string"}',
  response_body: '{"id": "integer", "name": "string", "email": "string"}'
})
```

### list_api_contracts

Liste les contrats d'API d'un projet.

```javascript
mcp__keymex-hub__list_api_contracts({
  project_id: 10
})
```

### get_api_contract

Recupere un contrat d'API avec ses endpoints.

```javascript
mcp__keymex-hub__get_api_contract({
  contract_id: 1
})
```

### search_api_endpoints

Recherche des endpoints dans tous les contrats.

```javascript
mcp__keymex-hub__search_api_endpoints({
  project_id: 10,
  query: "users"
})
```

### generate_openapi_spec

Genere une specification OpenAPI 3.0 pour un contrat.

```javascript
mcp__keymex-hub__generate_openapi_spec({
  contract_id: 1
})
```

---

## Commandes Rapides

### create_quick_command

Cree une commande rapide de scaffolding.

```javascript
mcp__keymex-hub__create_quick_command({
  name: "make:livewire",
  description: "Genere un composant Livewire complet",
  category: "laravel",
  template: `<?php
namespace App\\Livewire\\{{Module}};
class {{Name}} extends Component { }`,
  output_path_pattern: "app/Livewire/{{Module}}/{{Name}}.php",
  placeholders: '{"Module": "Nom du module", "Name": "Nom du composant"}',
  project_id: 10
})
```

### preview_quick_command

Previsualise le resultat d'une commande sans l'executer.

```javascript
mcp__keymex-hub__preview_quick_command({
  command_id: 5,
  params: { Module: "Dashboard", Name: "Stats" }
})
```

### execute_quick_command

Execute une commande rapide avec les parametres fournis.

```javascript
mcp__keymex-hub__execute_quick_command({
  command_id: 5,
  params: { Module: "Dashboard", Name: "Stats" }
})
```

### list_quick_commands

Liste les commandes rapides disponibles.

```javascript
mcp__keymex-hub__list_quick_commands({
  project_id: 10,
  category: "laravel"  // Optionnel
})
```

---

## Contexte et Restauration

### get_restore_context

**UTILISER AU DEMARRAGE.** Recupere le contexte complet pour reprendre le travail.

```javascript
mcp__keymex-hub__get_restore_context({
  path: "/home/user/mon-projet",
  depth: "full",      // minimal, standard, full
  focus: "frontend"   // Optionnel, domaine a prioriser
})
```

| Depth | Contenu |
|-------|---------|
| `minimal` | Resume, session active, todos en cours |
| `standard` | + conventions, patterns |
| `full` | + knowledge, changelog, tous les details |

### save_conversation_summary

**UTILISER AVANT DE TERMINER.** Sauvegarde un resume de la conversation.

```javascript
mcp__keymex-hub__save_conversation_summary({
  path: "/home/user/mon-projet",
  summary: "Implementation du module de signature email",
  key_decisions: [
    "Utiliser templates HTML pour les signatures",
    "Stocker les personnalisations en base"
  ],
  key_learnings: [
    "MongoDB dates en UTC",
    "Storage non versionne avec git"
  ],
  files_discussed: [
    "app/Services/SignatureGeneratorService.php",
    "app/Livewire/Signature/MySignature.php"
  ],
  unfinished_tasks: [
    "Ajouter export PDF",
    "Tests unitaires"
  ],
  important_context: "Le logo doit etre dans public/ pour etre deploye"
})
```

### get_project_snapshot

Recupere un snapshot complet du projet.

```javascript
mcp__keymex-hub__get_project_snapshot({
  project_id: 10
})
```

### get_context_stats

Recupere les statistiques du Code Memory.

```javascript
mcp__keymex-hub__get_context_stats({
  project_id: 10
})
```

### detect_project

Detecte automatiquement le projet a partir d'un chemin de fichier.

```javascript
mcp__keymex-hub__detect_project({
  file_path: "/home/user/mon-projet/app/Models/User.php"
})
```

---

## Integration Dokploy

### Configuration Serveur

#### dokploy_add_server

Enregistre un nouveau serveur Dokploy.

```javascript
mcp__keymex-hub__dokploy_add_server({
  name: "Production",
  url: "https://dokploy.example.com",
  api_token: "token-genere-dans-dokploy",
  description: "Serveur de production",
  is_default: true
})
```

#### dokploy_list_servers

Liste tous les serveurs Dokploy enregistres.

```javascript
mcp__keymex-hub__dokploy_list_servers({})
```

#### dokploy_server_status

Verifie la sante d'un serveur Dokploy.

```javascript
mcp__keymex-hub__dokploy_server_status({
  server_id: 1
})
```

### Projets et Applications

#### dokploy_list_projects

Liste tous les projets sur un serveur Dokploy.

```javascript
mcp__keymex-hub__dokploy_list_projects({
  server_id: 1  // Optionnel si un seul serveur
})
```

#### dokploy_create_project

Cree un nouveau projet sur Dokploy.

```javascript
mcp__keymex-hub__dokploy_create_project({
  name: "Marketing KEYMEX",
  description: "Application marketing",
  server_id: 1
})
```

#### dokploy_create_app

Cree une nouvelle application sur Dokploy.

```javascript
mcp__keymex-hub__dokploy_create_app({
  project_id: "dokploy-project-id",
  name: "marketing-app",
  description: "Application Laravel",
  server_id: 1
})
```

#### dokploy_app_info

Recupere les informations detaillees d'une application.

```javascript
mcp__keymex-hub__dokploy_app_info({
  application_id: "app-id",
  server_id: 1
})
```

### Deploiement

#### dokploy_deploy

Declenche un deploiement pour une application.

```javascript
mcp__keymex-hub__dokploy_deploy({
  application_id: "app-id",
  server_id: 1
})
```

#### dokploy_redeploy

Redeploit une application.

```javascript
mcp__keymex-hub__dokploy_redeploy({
  application_id: "app-id",
  server_id: 1
})
```

#### dokploy_start / dokploy_stop / dokploy_restart

Gere l'etat d'une application.

```javascript
mcp__keymex-hub__dokploy_start({ application_id: "app-id" })
mcp__keymex-hub__dokploy_stop({ application_id: "app-id" })
mcp__keymex-hub__dokploy_restart({ application_id: "app-id" })
```

#### dokploy_list_deployments

Liste l'historique des deploiements.

```javascript
mcp__keymex-hub__dokploy_list_deployments({
  application_id: "app-id",
  server_id: 1
})
```

### Variables d'Environnement

#### dokploy_set_env

Definit les variables d'environnement d'une application.

```javascript
mcp__keymex-hub__dokploy_set_env({
  application_id: "app-id",
  env: `APP_ENV=production
APP_DEBUG=false
DB_HOST=localhost`,
  server_id: 1
})
```

### Domaines

#### dokploy_list_domains

Liste les domaines configures pour une application.

```javascript
mcp__keymex-hub__dokploy_list_domains({
  application_id: "app-id",
  server_id: 1
})
```

#### dokploy_add_domain

Ajoute un domaine a une application.

```javascript
mcp__keymex-hub__dokploy_add_domain({
  application_id: "app-id",
  host: "app.example.com",
  https: true,
  certificate_type: "letsencrypt",  // none, letsencrypt, custom
  port: 8000,
  server_id: 1
})
```

### Base de Donnees

#### dokploy_create_database

Cree une base de donnees sur Dokploy.

```javascript
mcp__keymex-hub__dokploy_create_database({
  project_id: "dokploy-project-id",
  type: "postgres",  // postgres, mysql, mongo, redis, mariadb
  name: "marketing_db",
  password: "optional-password",
  docker_image: "postgres:16",
  server_id: 1
})
```

#### dokploy_database_info

Recupere les informations d'une base de donnees.

```javascript
mcp__keymex-hub__dokploy_database_info({
  database_id: "db-id",
  type: "postgres",
  server_id: 1
})
```

### Traefik Configuration

#### dokploy_read_traefik_config

Lit la configuration Traefik d'une application.

```javascript
mcp__keymex-hub__dokploy_read_traefik_config({
  application_id: "app-id",
  server_id: 1
})
```

#### dokploy_update_traefik_config

Met a jour la configuration Traefik.

```javascript
mcp__keymex-hub__dokploy_update_traefik_config({
  application_id: "app-id",
  traefik_config: "http:\n  routers:\n    ...",
  server_id: 1
})
```

### Liaison Projet

#### link_project_to_dokploy

Lie un projet Brain Memory a une application Dokploy.

```javascript
mcp__keymex-hub__link_project_to_dokploy({
  path: "/home/user/mon-projet",
  server_id: 1,
  dokploy_project_id: "dokploy-project-id",
  dokploy_app_id: "app-id",
  dokploy_app_name: "marketing-app",
  dokploy_app_type: "application",
  environment: "production",  // dev, staging, production
  auto_deploy: true
})
```

#### deploy_linked_project

Deploie un projet lie vers Dokploy.

```javascript
mcp__keymex-hub__deploy_linked_project({
  path: "/home/user/mon-projet",
  environment: "production"  // Optionnel
})
```

### API Generique

#### dokploy_api

Appelle n'importe quel endpoint de l'API Dokploy (tRPC).

```javascript
// Query (GET) - Lecture
mcp__keymex-hub__dokploy_api({
  endpoint: "settings.readTraefikConfig",
  method: "query"
})

// Mutation (POST) - Ecriture
mcp__keymex-hub__dokploy_api({
  endpoint: "application.deploy",
  params: { applicationId: "xxx" },
  method: "mutation"
})
```

#### dokploy_list_endpoints

Liste tous les endpoints Dokploy disponibles.

```javascript
mcp__keymex-hub__dokploy_list_endpoints({
  category: "application"  // Optionnel
})
```

---

## Fichier CLAUDE.md

### generate_claude_md

Genere et ecrit le fichier `.claude/CLAUDE.md` avec les instructions du projet.

```javascript
mcp__keymex-hub__generate_claude_md({
  path: "/home/user/mon-projet",
  include_conventions: true,
  include_decisions: true,
  include_agents: true,
  include_workflow: true,
  custom_instructions: "Instructions supplementaires..."
})
```

### preview_claude_md

Previsualise le contenu du fichier CLAUDE.md sans l'ecrire.

```javascript
mcp__keymex-hub__preview_claude_md({
  path: "/home/user/mon-projet"
})
```

### check_claude_md

Verifie si le fichier `.claude/CLAUDE.md` existe et retourne son contenu.

```javascript
mcp__keymex-hub__check_claude_md({
  path: "/home/user/mon-projet"
})
```

---

## Initialisation

### init_productivity_defaults

Initialise les snippets, regles de qualite et commandes par defaut.

```javascript
mcp__keymex-hub__init_productivity_defaults({})
```

---

## Bonnes Pratiques

### Workflow recommande

```
1. DEBUT SESSION
   └─> get_restore_context(depth: "full")

2. AVANT CHAQUE TACHE
   └─> get_recommended_agent(task_description)

3. PENDANT LE DEV
   ├─> add_decision (si decision architecturale)
   ├─> add_bug (si bug decouvert)
   ├─> add_todo (si tache identifiee)
   └─> save_code_knowledge (si apprentissage important)

4. APRES CHAQUE DEV SIGNIFICATIF
   └─> log_development_activity

5. FIN SESSION
   └─> save_conversation_summary
```

### Automatismes dans CLAUDE.md

```markdown
## AUTOMATISMES MCP

### Au DEBUT de chaque session
mcp__keymex-hub__get_restore_context({ path: "/chemin/projet", depth: "full" })

### A la FIN de chaque session
mcp__keymex-hub__save_conversation_summary({ path: "/chemin/projet", summary: "..." })
```

---

## Changelog

| Version | Date | Description |
|---------|------|-------------|
| 1.0.0 | 2026-01-06 | Documentation initiale |
