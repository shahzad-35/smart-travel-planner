# Page: Trip Wizard (create/edit)

- Single-column, max-w-4xl, focused: one decision per step.
- Stepper: active step gets primary fill + ring glow; connectors thin (h-0.5) and animate on progress.
- Grouped destination results (Countries / States & Provinces / Cities); states expand to city chips.
- Final CTA uses ACCENT (the one accent moment of this page); busy state with inline spinner mandatory.
- Errors inline near fields; flash errors via `x-ui.alert`/toast, never silent.
