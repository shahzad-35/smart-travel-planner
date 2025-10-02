# Repositories

This directory contains repository classes implementing the Repository pattern for data access.

## Structure

- **BaseRepository.php**: Interface defining CRUD contracts.
- **AbstractRepository.php**: Abstract class implementing common CRUD operations using Eloquent.
- **TripRepository.php**: Repository for Trip model with custom query methods.
- **FavoriteRepository.php**: Repository for Favorite model.
- **CollectionRepository.php**: Repository for Collection model.
- **TripExpenseRepository.php**: Repository for TripExpense model.

## Usage

Repositories are bound in the service container via `AppServiceProvider`. You can inject them into controllers or services like so:

```php
use App\Repositories\TripRepository;

class TripController extends Controller
{
    protected $tripRepository;

    public function __construct(TripRepository $tripRepository)
    {
        $this->tripRepository = $tripRepository;
    }

    public function index()
    {
        $trips = $this->tripRepository->all();
        return view('trips.index', compact('trips'));
    }
}
```

## Benefits

- Decouples data access logic from controllers and services.
- Provides a consistent interface for CRUD operations.
- Facilitates easier testing and maintenance.
