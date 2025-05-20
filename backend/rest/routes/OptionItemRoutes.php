<?php
require_once __DIR__ . '/../services/OptionItemService.php';

$optionService = new OptionItemService();

/**
 * @OA\Get(
 *     path="/optionitem",
 *     summary="Get all option items",
 *     tags={"OptionItems"},
 *     @OA\Response(response=200, description="List of option items")
 * )
 */
Flight::route('GET /optionitem', function () use ($optionService) {
    Flight::json($optionService->getAllOptionItems());
});

/**
 * @OA\Get(
 *     path="/optionitem/{id}",
 *     summary="Get option item by ID",
 *     tags={"OptionItems"},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Option item found"),
 *     @OA\Response(response=404, description="Option item not found")
 * )
 */
Flight::route('GET /optionitem/@id', function ($id) use ($optionService) {
    $option = $optionService->getOptionItemById($id);
    if ($option) {
        Flight::json($option);
    } else {
        Flight::halt(404, "Option item not found");
    }
});

/**
 * @OA\Post(
 *     path="/optionitem",
 *     summary="Create option item",
 *     tags={"OptionItems"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"question_id", "content", "is_correct"},
 *             @OA\Property(property="question_id", type="integer", example=1),
 *             @OA\Property(property="content", type="string", example="H2O"),
 *             @OA\Property(property="is_correct", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(response=200, description="Option item created")
 * )
 */
Flight::route('POST /optionitem', function () use ($optionService) {
    $data = Flight::request()->data->getData();
    try {
        $created = $optionService->createOptionItem($data);
        Flight::json(["success" => true, "message" => "Option item created", "option_id" => $created['last_insert_id']]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Put(
 *     path="/optionitem/{id}",
 *     summary="Update option item",
 *     tags={"OptionItems"},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="content", type="string", example="CO2"),
 *             @OA\Property(property="is_correct", type="boolean", example=false)
 *         )
 *     ),
 *     @OA\Response(response=200, description="Option item updated")
 * )
 */
Flight::route('PUT /optionitem/@id', function ($id) use ($optionService) {
    $data = Flight::request()->data->getData();
    try {
        $optionService->updateOptionItem($id, $data);
        Flight::json(["success" => true, "message" => "Option item updated"]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Delete(
 *     path="/optionitem/{id}",
 *     summary="Delete option item",
 *     tags={"OptionItems"},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Option item deleted")
 * )
 */
Flight::route('DELETE /optionitem/@id', function ($id) use ($optionService) {
    try {
        $optionService->deleteOptionItem($id);
        Flight::json(["success" => true, "message" => "Option item deleted"]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Get(
 *     path="/optionitem/question/{question_id}",
 *     summary="Get option items by question ID",
 *     tags={"OptionItems"},
 *     @OA\Parameter(name="question_id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Options found")
 * )
 */
Flight::route('GET /optionitem/question/@question_id', function ($question_id) use ($optionService) {
    Flight::json($optionService->getOptionsByQuestionId($question_id));
});

/**
 * @OA\Get(
 *     path="/optionitem/check/{question_id}/{content}",
 *     summary="Check option content by question ID",
 *     tags={"OptionItems"},
 *     @OA\Parameter(name="question_id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Parameter(name="content", in="path", required=true, @OA\Schema(type="string")),
 *     @OA\Response(response=200, description="Option found"),
 *     @OA\Response(response=404, description="Option not found")
 * )
 */
Flight::route('GET /optionitem/check/@question_id/@content', function ($question_id, $content) use ($optionService) {
    $result = $optionService->checkOptionContent($question_id, $content);
    if ($result) {
        Flight::json($result);
    } else {
        Flight::halt(404, "Option content not found for this question");
    }
});
