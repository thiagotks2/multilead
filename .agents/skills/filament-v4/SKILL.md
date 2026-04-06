---
name: filament-v4-fixer
description: Regras de ouro para Filament v4 e PHPUnit 11 no container multilead.
---
# Filament v4 & Laravel 12 Rules

Sempre que for gerar código Filament ou Testes, siga estas diretrizes:

## 1. **Namespaces de UI:**
- Use `Filament\Forms\Components` e `Filament\Tables\Columns`. NUNCA use `Filament\Resources\Form`.
- **Forms/Infolists:** Use sempre `Filament\Schemas\Schema`.
- **Método:** `public static function form(Schema $schema): Schema { return $schema->components([...]); }`.
- **Namespaces:** `Filament\Forms\Components` (Campos) e `Filament\Tables\Columns` (Colunas).
2. **Filament v4 Schema:** Em Resources, use `public static function form(Schema $schema): Schema` e o método `->components([...])`.
3. **PHPUnit 11:** Use obrigatoriamente o atributo `#[Test]`. Retornos devem ser `: void`.

### PROIBIDO:
- Criar arquivos de Filament ou Laravel usando a ferramenta de escrita de arquivo (`write_file`).
- Você DEVE usar o terminal para rodar os comandos `docker exec --user www-data multilead php artisan make:...`.

### COMANDO DE EXECUÇÃO (OBRIGATÓRIO):
Sempre use este formato para rodar comandos:
`docker exec --user www-data multilead php artisan {comando}`

### FLUXO DE CRIAÇÃO:
1. Execute o comando `make:filament-resource` via docker exec.
2. Após o sucesso, use `read_file` para ver o que o Artisan gerou.
3. Use `edit_file` para implementar a lógica desejada.
4. Use `vendor/bin/pint --dirty --format agent` se vc criou ou editou arquivos laravel/php.
5. **Finalização:** Rode o teste com `docker exec --user www-data multilead php artisan test --filter {Nome}`.

## 2. Testing Patterns (CRITICAL)
O Flash costuma usar helpers antigos. Siga estes padrões da v4:

### Setup de Teste:
- **Traits:** Use `InteractsWithResources`, `InteractsWithPages`, ou `InteractsWithSchemas` conforme o contexto.
- **Boot:** Certifique-se de que o usuário está autenticado com `$this->actingAs($user)`.

### Assertions Comuns:
- **Tables:** `livewire(ListLeads::class)->assertCanSeeTableRecords($leads)`.
- **Forms:** `livewire(CreateLead::class)->fillForm(['name' => 'Thiago'])->call('create')->assertHasNoFormErrors()`.
- **Actions:** `livewire(EditLead::class, ['record' => $id])->callAction(DeleteAction::class)`.

### FLUXO OBRIGATÓRIO PARA TESTES:
1. **Validar Sintaxe:** Antes de escrever o teste, use o MCP `search-docs` com a query: `["testing resources", "testing tables"]`.
2. **Gerar Boilerplate:** `docker exec --user www-data multilead php artisan make:test {Name}Test`.
3. **Implementar:** Use os links de referência internos para estruturar o `livewire()` test helper.
4. **Executar:** `docker exec --user www-data multilead php artisan test --filter {Name}Test`.