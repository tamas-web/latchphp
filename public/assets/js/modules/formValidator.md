# `formValidator.js` — usage reference

A zero-dependency, native form validator (no jQuery, no third-party lib). It mirrors validation on the
client purely for UX — **the server stays authoritative**. On a valid submit it dispatches the existing
`dynamicFormsSubmit` event, which `formHandler.js` listens for to perform the AJAX request, so the
submit pipeline is unchanged.

## When it validates
- **On submit** — validates every `[data-rule]` field; submits (dispatches `dynamicFormsSubmit`) only if
  all pass. If any fail, it **smooth-scrolls to and focuses the first invalid field**.
- **On blur** (`focusout`) — validates the field the user just left.
- **On change** — validates the field when its value is committed (essential for `<select>`, checkboxes,
  radios and date inputs, which fire `change` rather than `input`).
- **On input** — re-validates live as the user types, from the first interaction (no need to blur first);
  feedback appears and clears immediately.

Listeners are delegated on the form, so dynamically added fields (e.g. cloned components) are covered.

## Wiring (once, in `main.js`)
```js
import formValidator from './modules/formValidator.js';

new formValidator();                 // defaults to selector '.dynamic-form'
// new formValidator( '.my-forms' ); // or a custom selector
```
It auto-adds `novalidate` to each matched form and binds the listeners.

## Declaring rules on a field
One attribute, pipe-separated; arguments use `name:arg`.
```html
<input class="form-control" name="email" data-rule="required|email">
```

### Available rules
| Rule | Meaning |
|---|---|
| `required` | non-empty (after trim) |
| `email` | valid email |
| `number` | integer/decimal, e.g. `-12.5` |
| `phone` | optional `+`, then at least 8 of digits / space / `(` `)` / `-` |
| `password` | at least one letter **and** one digit **and** one special char |
| `min:N` | length ≥ N |
| `max:N` | length ≤ N |
| `matches:fieldName` | equals the value of `[name="fieldName"]` (e.g. password confirm) |
| `regexp` | tests against the pattern in `data-regexp` (see below) |

### ⚠ Empty-skip semantics (important)
**Only `required` enforces presence.** Every *format* rule (`email`, `number`, `password`, `min`, …)
**passes when the field is empty**, so an optional field only validates once it's filled. To require *and*
validate, combine them:
```html
<input name="email" data-rule="email">           <!-- optional, but must look like an email if filled -->
<input name="email" data-rule="required|email">  <!-- mandatory AND must be valid -->
```

## Messages (resolution order)
For a failing rule the message is: **`data-msg-<rule>`** → **`data-msg`** → built-in default (English).
```html
<input name="email"
       data-rule="required|email"
       data-msg-required="The email address is required"
       data-msg-email="The email address is not valid">

<input name="phone" data-rule="phone" data-msg="Invalid phone number">   <!-- one fallback for any rule -->
```
Built-in defaults: `required` → "This field is required", `email` → "The email address is not
valid", `password` → "The password must contain at least one letter, one digit and one special character", etc.

## The `regexp` rule (custom patterns)
The pattern lives in its own attribute so regex `|` / `:` can't collide with the rule separators:
```html
<input name="code" data-rule="required|regexp"
       data-regexp="^[0-9]{13}$"
       data-msg-regexp="Enter 13 digits">
```

## Password-confirm example
```html
<input type="password" name="password"         data-rule="required|min:8|password">
<input type="password" name="password_confirm" data-rule="required|matches:password"
       data-msg-matches="The passwords do not match">
```

## Via the HTML `Builder` (the app way)
`dataset` keys become `data-*` attributes, so in PHP:
```php
[
	'elementType' => 'input',
	'type'        => 'email',
	'name'        => 'email',
	'class'       => 'form-control',
	'dataset'     => [
		'rule'         => 'required|email',
		'msg-required' => 'The email address is required',
		'msg-email'    => 'The email address is not valid',
	],
],
```
→ `data-rule="required|email" data-msg-required="…" data-msg-email="…"`. This is exactly the
`fv-*` → `data-rule` swap the migration applies across the form views + FormFlow's `FormRenderer`.

## Feedback / validation states
- **Invalid:** the field gets `.is-invalid` (red) and a `.invalid-feedback` message is placed **after the
  field's wrapper** — the enclosing `.input-group` or `.form-check` if there is one, otherwise the field
  itself — so it never splits an input-group's input + addon. Visibility is toggled explicitly. An
  existing `.invalid-feedback` right after that wrapper is reused (drop one there to control the spot).
- **Checkboxes / radios:** `required` checks the **checked** state (an unchecked box fails; for radios,
  whether any in the group is selected) — not the element's `value`.
- **Valid:** when a **non-empty** field passes, it gets `.is-valid` (green check / border) and `.is-invalid`
  is removed.
- **Neutral:** an **empty optional** field (passes only because format rules skip on empty) is left with
  neither class — no green on blank fields.

## Scope / non-goals
- No async/remote rules (e.g. "email already taken") — that check stays server-side and comes back via
  `form_errors` (which `formHandler.js` paints as `.is-invalid`).
- Cross-field live re-validation is limited to the edited field; `matches` is re-checked on the confirm
  field's own blur/input and again for the whole form on submit.
- It does not replace server validation — it's UX only.
