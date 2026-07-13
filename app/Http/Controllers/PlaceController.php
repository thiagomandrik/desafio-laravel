<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlaceRequest;
use App\Http\Requests\UpdatePlaceRequest;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class PlaceController extends Controller
{
    #[OA\Get(
        path: '/api/places',
        tags: ['Places'],
        summary: 'Lista lugares, com filtro opcional por nome',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'name', in: 'query', required: false, description: 'Filtro parcial e case-insensitive pelo nome', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de lugares', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Place'))]
            )),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index(Request $request)
    {
        $places = Place::query()
            ->when($request->filled('name'), fn ($query) => $query->where('name', 'ilike', "%{$request->string('name')}%"))
            ->paginate(15);

        return PlaceResource::collection($places);
    }

    #[OA\Post(
        path: '/api/places',
        tags: ['Places'],
        summary: 'Cria um lugar',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['name', 'slug', 'city', 'state'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Praia Mole'),
                new OA\Property(property: 'slug', type: 'string', example: 'praia-mole'),
                new OA\Property(property: 'city', type: 'string', example: 'Florianópolis'),
                new OA\Property(property: 'state', type: 'string', example: 'SC'),
            ]
        )),
        responses: [
            new OA\Response(response: 201, description: 'Lugar criado', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'data', ref: '#/components/schemas/Place')]
            )),
            new OA\Response(response: 422, description: 'Erro de validação'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function store(StorePlaceRequest $request)
    {
        $place = Place::create($request->validated());

        return (new PlaceResource($place))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/api/places/{place}',
        tags: ['Places'],
        summary: 'Exibe um lugar específico',
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'place', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Lugar encontrado', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'data', ref: '#/components/schemas/Place')]
            )),
            new OA\Response(response: 404, description: 'Lugar não encontrado'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function show(Place $place)
    {
        return new PlaceResource($place);
    }

    #[OA\Put(
        path: '/api/places/{place}',
        tags: ['Places'],
        summary: 'Atualiza um lugar',
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'place', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['name', 'slug', 'city', 'state'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Praia Mole'),
                new OA\Property(property: 'slug', type: 'string', example: 'praia-mole'),
                new OA\Property(property: 'city', type: 'string', example: 'Florianópolis'),
                new OA\Property(property: 'state', type: 'string', example: 'SC'),
            ]
        )),
        responses: [
            new OA\Response(response: 200, description: 'Lugar atualizado', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'data', ref: '#/components/schemas/Place')]
            )),
            new OA\Response(response: 404, description: 'Lugar não encontrado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function update(UpdatePlaceRequest $request, Place $place)
    {
        $place->update($request->validated());

        return new PlaceResource($place);
    }

    #[OA\Delete(
        path: '/api/places/{place}',
        tags: ['Places'],
        summary: 'Remove (soft delete) um lugar',
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'place', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Lugar removido'),
            new OA\Response(response: 404, description: 'Lugar não encontrado'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function destroy(Place $place)
    {
        $place->delete();

        return response()->noContent();
    }
}
