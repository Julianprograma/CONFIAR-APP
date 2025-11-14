// app/Models/Apartment.php
<?php
// ...

class Apartment extends Model
{
    // ...
    
    public function owner()
    {
        // La clave foránea es 'owner_id' en la tabla 'apartments'
        return $this->belongsTo(User::class, 'owner_id'); 
    }
    
    public function monthlyDues()
    {
        return $this->hasMany(MonthlyDue::class, 'apartment_id');
    }
}