# SOISY

## Second interaction  

### Setings section - instalment price connection
- Add serialised array field
- Add validation for instalments,
- Add validation for minimum amount
- Modify product and cart api call to use serialised array (configured instalment table)

### Checkout choose instalment period
- Add select field with possible instalments (add below the description)
- Show information from Soisy (instalment amount) next to instalment select
- Preselect instalment on configured instalment table
- Query loan amount based on selection (check request instalment with selected in admin panel before call)

### AsK SOISY
- Ask about option to make request with multiple instalment periods.
