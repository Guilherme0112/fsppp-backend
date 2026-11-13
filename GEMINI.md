🚀 Diretrizes Arquiteturais do Projeto (gemini.md)Este repositório segue uma arquitetura baseada nos princípios de Clean Architecture, Domain-Driven Design (DDD) e SOLID.Toda nova feature ou endpoint deve ser desenvolvida de maneira desacoplada, garantindo que as regras de negócio residam exclusivamente no domínio, sem acoplamento direto com o framework Laravel ou com ferramentas externas.🛠️ Fluxo Padrão de Execução de uma FeatureCada endpoint deve respeitar estritamente a seguinte esteira de processamento:[ Rota Módulo (routes/v1/{modulo}.php) ]
                   ↓
 [ Form Request (Validação da Requisição) ]
                   ↓
  [ Controller do Módulo (Método Específico) ]
                   ↓
     [ DTO::deArray($dadosValidados) ]
                   ↓
 [ Use Case Específico (1 por Endpoint) ]
                   ↓
[ Entidade do Domínio (Regras de Negócio) ]
                   ↓
 [ Repositório Concreto / Driver de Infra ]
📐 Regras e Princípios ArquiteturaisRotas Isoladas por Módulo e Versionadas (routes/v1/):As rotas ficam na pasta routes/v1/ separadas por arquivos específicos para cada módulo em kebab-case (ex: solicitacao-pagamento.php, tarefa.php, usuario.php).O arquivo routes/v1/api.php atua como agregador principal importando/requerendo todos os arquivos de módulos.Controllers por Entidade ou Sub-recurso (Com múltiplos métodos):Cada módulo pode possuir um ou mais Controllers organizados por entidade ou sub-recurso (ex: TarefaController, QuadroController, StatusQuadroController) para gerenciar seus endpoints.Cada método do Controller corresponde a uma ação/endpoint específico.Cada método injeta o seu FormRequest e o seu Use Case específico.Um Use Case por Endpoint:Exclusividade: Cada método do Controller chama um Use Case exclusivo responsável por aquela operação específica.O Controller extrai os dados validados via FormRequest, converte para DTO via DTO::deArray(), passa os identificadores de contexto (usuarioId, pagamentoId, etc.) e executa o Use Case.Endpoints de Relacionamento (Ex: Usuários Disponíveis para Vinculação):Quando uma entidade exigir vincular ou relacionar usuários (ex: atribuir responsável por uma tarefa, aprovar solicitação, etc.), deve haver um endpoint dedicado no módulo correspondente que consulte quais usuários estão disponíveis/aptos para aquele relacionamento.O Use Case de usuários disponíveis consulta as abstrações necessárias (ex: UsuarioRepositoryInterface filtrando por perfil, status ativo ou permissões do módulo) e retorna um DTO leve de listagem (ex: id, nome, email, cargo).DTOs (Data Transfer Objects):Residem na camada de aplicação da Core.Definidos como readonly.Contêm obrigatoriamente o método estático public static function deArray(array $dados): self para conversão a partir de arrays validados.Camada de Domínio (Entidades e Regras de Negócio):Código PHP Puro (sem imports de Illuminate\*).Regras e validações de invariantes corporativas ficam dentro das Entidades e Value Objects.Princípio da Responsabilidade Única (SRP) e OCP garantidos no domínio.Camada app/ (Infraestrutura Concreta):Contém a implementação concreta das interfaces da Core (ex: app/Repositories/Eloquent/PagamentoEloquentRepository.php).🧪 Estratégia e Cobertura Rígida de Testes (Detector de Bugs)Regra de Ouro: Nenhuma feature ou bugfix é aceita sem a cobertura correspondente de testes automatizados projetados para tentar quebrar a aplicação e antecipar falhas de borda (edge cases).1. Níveis de Teste ExigidosTestes Unitários de Domínio (Entidades):Testam as validações internas e regras de negócio da entidade sem banco de dados ou mocks.Devem testar valores inválidos, estados inconsistentes e limites (boundary testing).Testes Unitários de Aplicação (Use Cases):Testam o fluxo do Use Case isolando os repositórios através de Mocks / Stubs (ex: Pest/PHPUnit Mocks).Devem garantir o comportamento quando a entidade lança exceções ou quando recursos não são encontrados.Testes de Integração / Feature (Controllers & FormRequests):Testam os endpoints HTTP completos via Pest ou PHPUnit chamando $this->json().Validam regras de autorização/autenticação, erros de validação do FormRequest (status 422), retornos de erro de domínio (400/404) e fluxo de sucesso (200/201/204).2. Matriz de Cobertura Obrigatória por FeatureCategoriaO que testarCaminho FelizPayload correto, usuário autorizado, resposta HTTP esperada e dados persistidos.Borda / LimitesValores zerados, negativos, strings extremamente longas, tipos incorretos.Validação (422)Campos obrigatórios ausentes, formatos inválidos (e-mail, UUIDs, datas).Invariantes do DomínioRegras de negócio violadas (ex: aprovar pagamento já pago, saldo insuficiente).Autorização (401/403)Token ausente, token expirado, usuário tentando alterar recurso de outro usuário.Não Encontrado (404)Identificadores inexistentes passados na URL.📁 Estrutura de Pastas EsperadaCore/ (ou src/)
├── Pagamentos/
│   ├── Domain/
│   │   ├── Entities/
│   │   │   └── Pagamento.php
│   │   ├── Repositories/
│   │   │   └── PagamentoRepositoryInterface.php
│   │   └── Exceptions/
│   │       └── PagamentoInvalidoException.php
│   └── Application/
│       ├── UseCases/
│       │   ├── CriarPagamentoUseCase.php
│       │   ├── ListarMeusPagamentosUseCase.php
│       │   ├── ListarPagamentosUseCase.php
│       │   ├── ListarUsuariosDisponiveisParaPagamentoUseCase.php
│       │   ├── EditarPagamentoUseCase.php
│       │   ├── ApagarPagamentoUseCase.php
│       │   ├── AprovarPagamentoUseCase.php
│       │   └── RecusarPagamentoUseCase.php
│       └── DTOs/
│           ├── CriarPagamentoDTO.php
│           ├── EditarPagamentoDTO.php
│           └── ...
└── Usuarios/
    └── Domain/
        └── Repositories/
            └── UsuarioRepositoryInterface.php

app/
├── Http/
│   ├── Controllers/
│   │   └── Pagamentos/
│   │       └── PagamentoController.php
│   └── Requests/
│       └── Pagamentos/
│           ├── CriarPagamentoRequest.php
│           ├── ListarUsuariosDisponiveisRequest.php
│           └── ...
└── Repositories/
    └── Eloquent/
        ├── PagamentoEloquentRepository.php
        └── UsuarioEloquentRepository.php

routes/
└── v1/
    ├── api.php
    └── solicitacao-pagamento.php

tests/
├── Unit/
│   └── Core/
│       └── Pagamentos/
│           └── Application/
│               └── ListarUsuariosDisponiveisParaPagamentoUseCaseTest.php
└── Feature/
    └── Api/
        └── V1/
            └── PagamentoTest.php
💻 Exemplo de Referência: Módulo de Pagamentos (Com Endpoint de Relacionamento)1. Rotas do Módulo (routes/v1/solicitacao-pagamento.php)<?php

use App\Http\Controllers\Pagamentos\PagamentoController;
use Illuminate\Support\Facades\Route;

Route::prefix('pagamentos')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [PagamentoController::class, 'criarPagamento']);
    Route::get('/meus', [PagamentoController::class, 'listarMeusPagamentos']);
    Route::get('/', [PagamentoController::class, 'listarPagamentos']);
    
    // Endpoint para buscar usuários elegíveis para atribuição/relacionamento
    Route::get('/usuarios-disponiveis', [PagamentoController::class, 'listarUsuariosDisponiveis']);
    
    Route::put('/{pagamentoId}', [PagamentoController::class, 'editarPagamento']);
    Route::delete('/{pagamentoId}', [PagamentoController::class, 'apagarPagamento']);
    Route::patch('/{pagamentoId}/aprovar', [PagamentoController::class, 'aprovarPagamento']);
    Route::patch('/{pagamentoId}/recusar', [PagamentoController::class, 'recusarPagamento']);
});
2. Controller do Módulo (app/Http/Controllers/Pagamentos/PagamentoController.php)<?php

namespace App\Http\Controllers\Pagamentos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pagamentos\ListarUsuariosDisponiveisRequest;
use Core\Pagamentos\Application\DTOs\FiltroUsuariosDisponiveisDTO;
use Core\Pagamentos\Application\UseCases\ListarUsuariosDisponiveisParaPagamentoUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PagamentoController extends Controller
{
    // Métodos anteriores (criarPagamento, listarMeusPagamentos, etc.)

    public function listarUsuariosDisponiveis(
        ListarUsuariosDisponiveisRequest $request,
        ListarUsuariosDisponiveisParaPagamentoUseCase $useCase
    ): JsonResponse {
        $dto = FiltroUsuariosDisponiveisDTO::deArray($request->validated());
        $output = $useCase->executar($dto);

        return response()->json($output, Response::HTTP_OK);
    }
}
3. Request de Validação de Filtros (app/Http/Requests/Pagamentos/ListarUsuariosDisponiveisRequest.php)<?php

namespace App\Http\Requests\Pagamentos;

use Illuminate\Foundation\Http\FormRequest;

class ListarUsuariosDisponiveisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'busca' => ['nullable', 'string', 'max:100'],
            'funcao' => ['nullable', 'string', 'in:APROVADOR,GESTOR,OPERADOR'],
        ];
    }
}
4. DTO de Filtro (Core/Pagamentos/Application/DTOs/FiltroUsuariosDisponiveisDTO.php)<?php

namespace Core\Pagamentos\Application\DTOs;

readonly class FiltroUsuariosDisponiveisDTO
{
    public function __construct(
        public ?string $busca = null,
        public ?string $funcao = null
    ) {}

    public static function deArray(array $dados): self
    {
        return new self(
            busca: $dados['busca'] ?? null,
            funcao: $dados['funcao'] ?? null
        );
    }
}
5. Use Case de Consulta de Relacionamento (Core/Pagamentos/Application/UseCases/ListarUsuariosDisponiveisParaPagamentoUseCase.php)<?php

namespace Core\Pagamentos\Application\UseCases;

use Core\Pagamentos\Application\DTOs\FiltroUsuariosDisponiveisDTO;
use Core\Usuarios\Domain\Repositories\UsuarioRepositoryInterface;

class ListarUsuariosDisponiveisParaPagamentoUseCase
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}

    public function executar(FiltroUsuariosDisponiveisDTO $dto): array
    {
        // Busca usuários ativos com permissões de pagamento através da abstração do repositório
        $usuarios = $this->usuarioRepository->buscarUsuariosAtivosParaPagamento(
            termoBusca: $dto->busca,
            funcao: $dto->funcao
        );

        return array_map(fn($usuario) => [
            'id' => $usuario->getId(),
            'nome' => $usuario->getNome(),
            'email' => $usuario->getEmail(),
            'funcao' => $usuario->getFuncao(),
        ], $usuarios);
    }
}
🧪 Exemplo de Teste para o Endpoint de Relacionamento (tests/Feature/Api/V1/PagamentoTest.php)<?php

use App\Models\User;
use function Pest\Laravel\{actingAs, getJson};

test('deve listar os usuarios disponiveis para relacionamento no modulo de pagamentos', function () {
    $admin = User::factory()->create(['role' => 'APROVADOR', 'active' => true]);
    User::factory()->create(['role' => 'OPERADOR', 'active' => true]);

    actingAs($admin)
        ->getJson('/api/v1/pagamentos/usuarios-disponiveis?funcao=APROVADOR')
        ->assertStatus(200)
        ->assertJsonFragment([
            'id' => $admin->id,
            'nome' => $admin->name,
        ]);
});
