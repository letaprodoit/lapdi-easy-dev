# Known issues

Running log of things that still need fixing in LAPDI Easy Dev. Add a new entry
under **Open** when something needs doing; move it to **Resolved** (with the
release or commit it was fixed in) once it's done.

## Open

### 1. Replace `lab.letaprodoit.com` links with the appropriate Jira links

The plugin still points documentation, API, and support links at the old
`lab.letaprodoit.com` lab site. These need to be updated to the corresponding
Jira URLs.

Locations to update:

| File | Line | What it is |
|------|------|------------|
| `TSP_Easy_Dev.autoload.php` | 86 | `TSP_LAB_URL` define — the base URL; also feeds `TSP_LAB_BUG_URL` (line 92), which is the plugin's **Support** link (`tsp-easy-dev.php:47`). Updating this one define fixes the Support link everywhere. |
| `TSP_Easy_Dev.autoload.php` | 5 | Plugin **Description** header — "Framework Docs" link. |
| `tsp-easy-dev.php` | 5 | Plugin **Description** header — duplicate of the above; keep both in sync. |
| `README.md` | 6, 16, 22 | GitHub-facing docs/API/issue links. |
| `README.txt` | 56, 66 | WordPress.org-facing documentation links (framework docs and examples). |

Notes:
- `TSP_LAB_BUG_URL` (`TSP_Easy_Dev.autoload.php:92`) is derived from `TSP_LAB_URL`
  as `TSP_LAB_URL . "%PLUGIN%/issues/new"`. If Jira uses a different issue-creation
  path, update this pattern too rather than only the base URL.
- `.idea/workspace.xml` also contains a `lab.letaprodoit.com` reference, but that is
  local IDE configuration and is **not** shipped in the plugin — ignore it for
  release purposes.

## Resolved

_(none yet)_
