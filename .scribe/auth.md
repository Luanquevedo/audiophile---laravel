# Authenticating requests

To authenticate requests, include a **`Cookie`** header with the value **`"{SESSION_COOKIE}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Esta API usa Sanctum em modo cookie/sessão. Faça login e reutilize os cookies (ou use "Try it out").
