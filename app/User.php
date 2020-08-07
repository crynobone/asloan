<?php

namespace App;

use App\Actions\ApplyLoan;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Money\Money;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * User has many loans.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loans()
    {
        return $this->hasMany(Loan::class, 'user_id', 'id');
    }

    /**
     * Apply for new Loan.
     */
    public function applyLoan(
        ?string $description,
        Money $total,
        CarbonInterface $termEndedAt,
        ?CarbonInterface $termStartedAt = null,
        ?int $paymentInterval = null
    ): Loan {
        $applyLoan = \app(ApplyLoan::class);

        return $applyLoan($this, $description, $total, $termEndedAt, $termStartedAt, $paymentInterval);
    }
}
