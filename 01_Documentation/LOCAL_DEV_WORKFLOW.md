# Local Development Workflow

How the **Ave Child theme** is version-controlled and consumed by LocalWP.

This is the authoritative description of the development workflow. Read it
before editing any theme code. It explains where the canonical code lives, how
LocalWP runs it, and how to repair the link if it is ever lost.

---

## TL;DR

- The **only** physical copy of the child theme lives in this repo at
  [`07_Source/Themes/ave-child/`](../07_Source/Themes/ave-child/).
- LocalWP runs that exact folder through a **Windows directory junction**.
- **Edit theme files in the repo.** Git tracks them; LocalWP serves them live.
- If the link breaks, run [`scripts/link-child-theme.ps1`](../scripts/link-child-theme.ps1).

---

## Old workflow (the problem)

```
Repo (Git)                         LocalWP (real folder, NOT in Git)
07_Source/Themes/ave-child/        app/public/wp-content/themes/ave-child/
  stale 4-file copy                  <-- all real development happened here
```

- All edits happened **inside LocalWP**, outside Git.
- The repo held a **stale, partial** copy (no `inc/`, no `assets/`).
- Two diverging copies → no version control of the work that mattered.

## New workflow (the fix)

```
Repo (Git) — SINGLE SOURCE OF TRUTH        LocalWP — consumer
07_Source/Themes/ave-child/   <───JUNCTION─── app/public/wp-content/themes/ave-child
  functions.php   (tracked)                    (reparse point → repo copy)
  header.php      (tracked)
  style.css       (tracked)
  screenshot.jpg  (tracked)
  inc/            (tracked)
  assets/         (tracked)
```

- One physical copy, in the repo, under Git.
- LocalWP's `ave-child` is a **junction** that resolves to the repo copy.
- Editing the repo copy updates the running site instantly (same bytes on disk).

---

## Why a junction (and not a symlink, copy, or reverse link)

| Option | Why not |
| ------ | ------- |
| **Symlink** (`mklink /D`) | Needs Administrator **or** Windows *Developer Mode*. Developer Mode is **off** on this machine, so symlinks would require elevation for every operation. |
| **Manual copy / deploy script** | Creates two physical copies → drift and "forgot to sync". Not live. |
| **Reverse junction** (repo points into LocalWP) | Git handles junctions poorly and could traverse into the whole WordPress install → risks tracking files **outside** the child theme. |
| **`.git` inside LocalWP** | Mixes version control into the WordPress install; tooling (GitHub Desktop, VS Code) targets this repo, not the WP tree. |
| **Directory junction** (`mklink /J`) — **CHOSEN** | No admin, no Developer Mode. Transparent to Apache/PHP/WordPress. Repo holds the only real copy, so Git tracks **only** the theme. Both paths are on the same NTFS volume (C:), which junctions require. |

**Direction is deliberate:** the junction lives in **LocalWP** and targets the
**repo**. The source of truth is therefore in Git; LocalWP is only a consumer;
Git never sees anything outside the theme.

---

## Repository structure (relevant parts)

```
travel-without-borders/                      <-- Git repo root
├── 01_Documentation/
│   └── LOCAL_DEV_WORKFLOW.md                 <-- this file
├── 07_Source/
│   └── Themes/
│       └── ave-child/                        <-- CANONICAL child theme (edit here)
│           ├── functions.php
│           ├── header.php
│           ├── style.css
│           ├── screenshot.jpg
│           ├── inc/
│           │   └── hero-carousel.php
│           └── assets/
│               ├── css/hero-carousel.css
│               └── js/hero-carousel.js
└── scripts/
    └── link-child-theme.ps1                  <-- (re)create the junction
```

## LocalWP structure (relevant parts)

```
C:\Users\pskan\Local Sites\travelwithoutborders\
└── app\public\                               <-- WordPress web root
    └── wp-content\themes\
        ├── ave\                              <-- parent theme (vendor; untouched, real folder)
        └── ave-child\   ──JUNCTION──>  <repo>\07_Source\Themes\ave-child
```

Only the **child** theme is a junction. The parent `ave` theme stays a normal,
vendor-owned folder and is never edited or committed.

---

## Who does what

| Actor | Path it uses | Role |
| ----- | ------------ | ---- |
| **Claude Code / VS Code / GitHub Desktop** | `07_Source/Themes/ave-child/` (repo) | **Edit here.** This is where Git tracks changes. |
| **Git** | Repo working tree only | Tracks the child theme + docs. Never traverses the junction (the junction lives outside the repo). |
| **LocalWP (Apache + PHP)** | `wp-content/themes/ave-child` (junction) | Reads the repo files transparently and serves the live site. |
| **WordPress** | active theme `ave-child` (DB option unchanged) | Loads the child by folder name; the junction makes that name resolve to the repo. |

> **Editing through either path is the same bytes on disk.** Standardise on the
> **repo path** so changes are obvious to Git. Editing via the LocalWP junction
> path also works (it writes through to the repo), but prefer the repo path.

---

## Daily workflow

1. Open the repo in VS Code / GitHub Desktop.
2. Edit files under `07_Source/Themes/ave-child/`.
3. Refresh `http://travelwithoutborders.local/` — changes are live immediately.
4. Commit from the repo as normal. Keep commits scoped to the theme + docs.

No build step, no copy step, no sync step.

---

## Recovering the link

The junction can be lost if the repo is cloned fresh, moved, or if LocalWP
recreates a real `ave-child` folder. To restore it:

```powershell
# From the repo root, in PowerShell:
./scripts/link-child-theme.ps1
```

The script is **idempotent** and **non-destructive**:

- If the correct junction already exists → it does nothing.
- If a wrong link exists → it recreates it.
- If a **real folder** is found → it **backs it up first** (timestamped,
  beside the themes folder) and then links. It never deletes real code blindly.

To verify the link manually:

```powershell
Get-Item "C:\Users\pskan\Local Sites\travelwithoutborders\app\public\wp-content\themes\ave-child" -Force |
  Select-Object LinkType, Target
# Expect: LinkType = Junction, Target = ...\07_Source\Themes\ave-child
```

---

## Guarantees / boundaries

- ✅ Live website, production, WordPress core, plugins, and the database are
  **untouched** by this workflow.
- ✅ Git tracks **only** the child theme (plus repo docs). Run
  `git ls-files | grep wp-content` — it returns nothing.
- ✅ The parent `ave` theme is never linked, edited, or committed.
- ⚠️ The junction is machine-local (an OS-level pointer). It is **not** stored
  in Git. On any new machine, run the recovery script once after cloning.
- ⚠️ A pre-junction safety backup of the live theme is kept at
  `…\Local Sites\travelwithoutborders\_theme-backups\` (outside the web root and
  outside Git).

---

## See also

- [Contributing](../CONTRIBUTING.md) — ground rules and where things go.
- [Coding Standards](CODING_STANDARDS.md) — child-theme-only, PHP 7.4, no inline CSS.
- [Local Setup](../05_Deployment/LOCAL_SETUP.md) — reconstructing the site locally.
