<?php

namespace Tests\Feature\Filament\CustomComponents;

use App\Filament\CustomComponents\Forms\PhoneInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Livewire\Component;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PhoneInputTest extends TestCase
{
    #[Test]
    public function it_can_render_phone_input_in_a_form(): void
    {
        Livewire::test(TestComponentWithPhoneInput::class)
            ->assertFormExists()
            ->assertFormFieldExists('phone');
    }

    #[Test]
    public function it_fails_validation_for_invalid_phone_in_form(): void
    {
        Livewire::test(TestComponentWithPhoneInput::class)
            ->fillForm(['phone' => 'abc'])
            ->call('save')
            ->assertHasFormErrors(['phone']);
    }
}

class TestComponentWithPhoneInput extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                PhoneInput::make('phone'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render()
    {
        return <<<'HTML'
        <div>
            {{ $this->form }}
        </div>
        HTML;
    }
}
