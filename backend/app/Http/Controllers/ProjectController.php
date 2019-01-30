<?php

/**
 * Project controller
 * 
 */

namespace App\Http\Controllers;

use Auth;
use App\Project;
use App\Flow;

class ProjectController extends Controller
{

    /**
     * Return all project data
     *
     * @param int $id Flow id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory List with projects objects
     */
    public function index()
    {
        return response()->json(Auth::user()->organization->projects);
    }

    /**
     * Return project data
     * 
     * @param int $id Project id
     * @return type \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory Project object
     */
    public function get($id)
    {
        if (!Project::userHasAccess($id)) {
            return response()->json(['message' => 'UNAUTHORIZED'], 401);
        }

        $project = Project::find($id);

        return response()->json($project);
    }

    /**
     * Save flow
     * 
     * @param \Illuminate\Http\Request $req
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory New project object
     */
    public function save(\Illuminate\Http\Request $req)
    {

        $project = new Project();

        if ($req->exists('id') && $req->id) {
            $project->id = $req->id;
            $project->exists = true;
        }

        $project->name = $req->name;
        $project->organization_id = Auth::user()->organization->id;

        $project->save();

        return response()->json($project);
    }

    /**
     * Deletes project
     * 
     * @param int $id Project id
     */
    public function delete($id)
    {
        if (!Project::userHasAccess($id)) {
            return response()->json(['message' => 'UNAUTHORIZED'], 401);
        }

        Project::where('id', $id)->update(array('deleted' => 1));
    }

    /**
     * Return project's flows pair id/name
     * 
     * @param int $id Project id
     * @return type \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory List of project's flows
     */
    public function getFlows($id)
    {
        if (!Project::userHasAccess($id)) {
            return response()->json(['message' => 'UNAUTHORIZED'], 401);
        }

        return response()->json(Project::find($id)->flowsNameId);
    }

    /**
     * Returns all fields id of the project
     * 
     * @param int $id Project id
     * @return array List with all fields id of the project
     */
    public function getFlowsFieldIds($id)
    {
        if (!Project::userHasAccess($id)) {
            return response()->json(['message' => 'UNAUTHORIZED'], 401);
        }

        $flows = Project::find($id)->flows;
        $fieldIds = array();
        foreach ($flows as $flow) {
            $flow = json_decode($flow->flow);
            if ($flow) {
                $nodes = $flow->nodes;

                foreach ($nodes as $node) {
                    if (property_exists($node, 'fieldId') && $node->fieldId) {
                        $fieldIds[] = $node->fieldId;
                    }
                }
            }
        }
        return array_unique($fieldIds);
    }

    /**
     * Returns all forms id of the project
     * 
     * @param int $id Project id
     * @return array List of forms id of the project
     */
    public function getFlowsFormIds($id)
    {
        if (!Project::userHasAccess($id)) {
            return response()->json(['message' => 'UNAUTHORIZED'], 401);
        }

        $flows = Project::find($id)->flows;
        $formIds = array();
        foreach ($flows as $flow) {
            $flow = json_decode($flow->flow);
            if ($flow) {
                $nodes = $flow->nodes;

                foreach ($nodes as $node) {
                    if (property_exists($node, 'formId') && $node->formId) {
                        $formIds[] = $node->formId;
                    }
                }
            }
        }
        return array_unique($formIds);
    }

    /**
     * Returns all nodes from all bot's flows
     * 
     * @param int $id Bot id
     * @return array Nodes
     */
    public function getAllNodes($id)
    {
        if (!Project::userHasAccess($id)) {
            return response('Unauthorized.', 401);
        }

        $flows = Project::find($id)->flows;
        $nodes = array();
        foreach ($flows as $flow) {
            $node = json_decode($flow->flow);
            if ($node) {
                $nodes = array_merge($nodes, $node->nodes);
            }
        }

        return $nodes;
    }

}
