# Rosca (Chama) Module — README

A production-ready Rosca (Chama) module scaffolded to follow Akaunting module conventions.  
Implements rotating-savings cycles: rosca setup, members, contributions, rounds, scheduled closing, winner selection, asynchronous payouts (pluggable gateway), ledger/audit trail, and a scaffold to integrate with Akaunting accounting.

This README contains quick install, config, diagrams and instructions to generate PNG fallbacks for the included SVG diagrams.

---

## Diagrams

Architecture and sequence diagrams are included under `modules/rosca/docs/` as SVGs and Mermaid source files. GitHub will render the SVGs inline. For compatibility we provide instructions to generate PNG fallbacks locally.

<picture>
  <source srcset="modules/rosca/docs/architecture.svg" type="image/svg+xml">
  <img src="modules/rosca/docs/architecture.png" alt="Rosca Architecture" style="max-width:100%;">
</picture>

<picture>
  <source srcset="modules/rosca/docs/sequence.svg" type="image/svg+xml">
  <img src="modules/rosca/docs/sequence.png" alt="Payout Sequence" style="max-width:100%;">
</picture>

Notes:
- If the PNG files are not yet present, use the commands below to generate them locally.

### Generate PNGs from SVG (recommended)

If you have `rsvg-convert` installed (from librsvg):

```bash
rsvg-convert -w 1600 -h 900 modules/rosca/docs/architecture.svg -o modules/rosca/docs/architecture.png
rsvg-convert -w 1600 -h 900 modules/rosca/docs/sequence.svg -o modules/rosca/docs/sequence.png
```

Or using Inkscape:

```bash
inkscape modules/rosca/docs/architecture.svg --export-type=png --export-width=1600 --export-height=900 -o modules/rosca/docs/architecture.png
inkscape modules/rosca/docs/sequence.svg --export-type=png --export-width=1600 --export-height=900 -o modules/rosca/docs/sequence.png
```

### Generate PNGs from Mermaid source (alternative)

If you prefer to generate diagrams from Mermaid source files included in `modules/rosca/docs/*.mmd` you can use `mmdc` (Mermaid CLI):

```bash
mmdc -i modules/rosca/docs/architecture.mmd -o modules/rosca/docs/architecture.png -w 1600 -H 900
mmdc -i modules/rosca/docs/sequence.mmd -o modules/rosca/docs/sequence.png -w 1600 -H 900
```

---

The rest of the README (installation, configuration, endpoints, lifecycle, testing, ops & security) is available in the module docs — see the `modules/rosca/README.md` or the root README previously added in this branch for full details.
