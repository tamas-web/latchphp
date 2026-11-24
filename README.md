# LatchPHP

**A lightweight, dependency-light PHP 8 MVC framework with a first-class plugin & hook system.**

No Laravel, no Symfony — a small, self-written core you can actually hold in your head, built
around the idea that *everything attaches onto a stable core*. Plugins **latch** on through a
WordPress-style hook system; they never edit core.

---

## Why LatchPHP

- 🪝 **Hooks first.** A global **Events** system (fire-and-forget) + **Filters** system
  (value transformers). Extend any part of the app without touching core.
- 🔌 **Real plugin system.** Install / activate / deactivate / uninstall lifecycle, zip-slip-safe
  uploads, ACL-gated management, asset publishing — plugins are first-class, not an afterthought.
- 🪶 **Tiny footprint.** Three runtime dependencies. No ORM, no query builder, no template engine —
  hand-rolled `mysqli` prepared statements and plain-PHP views.
- 🔐 **Security-minded.** Argon2id + peppered passwords, CSRF tokens bound to the logged-in user on
  every state-changing route, prepared statements for every application query, an output-escaping
  convention at the model and element layers, RBAC (roles → permissions) + a super-admin bitflag,
  an HMAC-signed auth cookie (`secure` / `httponly` / `samesite=Lax`) keyed on CSPRNG material, and
  uploaded files served behind an authorisation filter that denies by default.
- 🧱 **Batteries included.** Routing, middleware, auth/ACL, an HTML builder, notifications, a
  queued mailer, cron, backups, uploads/attachments, and a structured error handler with file logging.
- 🐳 **One-command setup.** Dockerized; PHP + Composer run inside the container.

---

## Quick start

Requires **Docker** + **docker compose**.

```bash
git clone https://github.com/tamas-web/latchphp.git
cd latchphp
cp .env.example .env      # set your secrets/salts
make setup                # build + start containers, install deps (in-container), seed the DB
```

Then open **https://localhost** (accept the self-signed cert) and sign in with the seeded
super-admin:

```
superadmin@example.com  /  Password123!
```

Useful targets (`make help` lists them all):

| Command | Does |
|---|---|
| `make setup` | First run: up + install + wait-db + seed |
| `make up` / `make down` | Start / stop containers |
| `make seed` | Reset the DB to a known state |
| `make migrate` | Apply `migrations/*.sql` |
| `make shell` / `make db` | Shell into web / database container |
| `make new-project <name>` | Spin off a clean, fresh copy of the source into `exports/` |

---

## A taste of the API

**A route** (`routes/*.php`) — gated by auth + a permission:

```php
Route::get( '/admin/reports', [ ReportsController::class, 'index' ] )
	->middleware( [
		MustBeLoggedIn::class,
		[ Can::class, 'reports.view' ],
	] );
```

**A plugin** (`plugins/MyPlugin/MyPlugin.php`) — latches onto the core via hooks:

```php
namespace Plugins\MyPlugin;

use App\Libraries\Plugin;
use App\Events\Listener;
use App\Filters\Filter;

class MyPlugin extends Plugin {

    public function run(){
        Listener::on( 'routes_loaded', [ $this, 'addRoutes' ] );   // register routes
        Filter::add( 'app.menu', [ $this, 'menuItem' ] );          // extend the sidebar
    }

    public function install() : void { /* CREATE TABLE IF NOT EXISTS, seed ACL ... */ }
}
```

**Hooks** — fire events, transform values:

```php
Emitter::broadcast( 'order_paid', $order );           // event (side effects)
$title = Filter::apply( 'page_title', $title );        // filter (transform a value)
```

---

## Project layout

```
app/         framework + application code (App\ namespace)
config/      app.php, database.php
helpers/     global helper functions (esc_html, env, filters)
routes/      route definitions (auto-loaded)
views/       plain-PHP templates
plugins/     installable plugins (Plugins\ namespace)
migrations/  idempotent SQL upgrades
database/    seed.php (roles, permissions, demo users)
public/      web root + assets
.docker/     Dockerfile + PHP config
```

## Where to start reading

| To understand | Read |
|---|---|
| How a request boots | [`app/Bootloader.php`](app/Bootloader.php) |
| Routing and middleware | [`app/Routing/`](app/Routing/) |
| The hook system everything attaches to | [`app/Events/`](app/Events/) + [`app/Filters/`](app/Filters/) |
| How a plugin latches on | [`plugins/HelloWorld/`](plugins/HelloWorld/) |

`HelloWorld` is the reference plugin and a complete working example: four files covering the hooks
most plugins start with — a filter, an event, and a page of its own at `/hello-world` with its own
route, controller and templates. The page documents each hook it uses as it renders. It deliberately
uses no database, no ACL and no published assets, so deactivating it leaves no trace; the `install()`
/ `uninstall()` lifecycle those need is on the `Plugin` base class. Copy it to start your own.

---

## Status & license

LatchPHP powers production admin panels and CRMs. It's a deliberately small framework —
contributions and ideas welcome.

Released under the [MIT License](LICENSE).

Built by **Mujnoi Tamas** — [github.com/tamas-web](https://github.com/tamas-web).
