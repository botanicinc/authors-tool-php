<?php
/**
 * Flow controller
 * 
 */
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Flow;
use App\Project;

class FlowController extends Controller
{

    /**
     * Return all flow data and metadata 
     *
     * @param int $id Flow id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function index($id)
    {
        if (!Flow::userHasAccess($id)) {
            return response()->json(['message' => 'UNAUTHORIZED'], 401);
        }

        return response()->json(Flow::find($id));
    }

    /**
     * Return flow data
     * 
     * @param int $id Flow id
     * @return type \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function get($id)
    {
        if (!Flow::userHasAccess($id)) {
            return response()->json(['message' => 'UNAUTHORIZED'], 401);
        }
        
        $flow = Flow::find($id);

        if (isset($flow['flow'])) {
            return response($flow['flow'])->header('Content-Type', "application/json; charset=utf-8");
        }

    }

    /**
     * Save flow
     * 
     * @param \Illuminate\Http\Request $req
     * @return Flow
     */
    public function save(\Illuminate\Http\Request $req)
    {

        $flow = new Flow();

        if ($req->flowId) {
            if (!Flow::userHasAccess($req->flowId)) {
                return response()->json(['message' => 'UNAUTHORIZED'], 401);
            }

            //check updated_at value
            $currentFlow = Flow::find($req->flowId);
            if (!$currentFlow) {
                return response()->json(['message' => 'FLOW_NOT_FOUND'], 404);
            }
            if ($req->exists('updatedAt') && $currentFlow->updated_at != $req->updatedAt) {
                return response()->json(['message' => 'UPDATEDAT_MISMATCH'], 409);
            }

            $flow->exists = true;
            $flow->id = $req->flowId;
        } else {
            if (!Project::userHasAccess($req->projectId)) {
                return response()->json(['message' => 'UNAUTHORIZED'], 401);
            }

            $flow->project_id = $req->projectId;
        }

        if ($req->exists('name')) {
            $flow->name = $req->name;
        }

        if ($req->exists('flow')) {
            $flow->flow = json_encode($req->flow, JSON_UNESCAPED_UNICODE);
        }
        $flow->save();

        return response()->json($flow);
    }

    /**
     * Delete flow
     * 
     * @param int $id Flow id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function delete($id)
    {
        if (!Flow::userHasAccess($id)) {
            return response()->json(['message' => 'UNAUTHORIZED'], 401);
        }

        $flow = Flow::find($id);
        $project = $flow->project_id;
        Flow::destroy($id);
        $project = Project::find($project);

        //now delete all flow connections pointing to it        
        foreach ($project->flows as $f) {
            $found = false;

            $rf = json_decode($f->flow);

            if (isset($rf->nodes)) {

                $nodes = $rf->nodes;
                foreach ($nodes as $k => $n) {

                    if ($n->type == 'flowId' && $n->flowId == $id) {

                        unset($rf->nodes[$k]);
                        $found = true;
                    }
                }
            }

            if ($found) {
                //now delete connections ponting to the deleted flow connections
                $this->deleteConnectionToNodeId($rf->nodes);
                $f->flow = json_encode($rf);
                echo $f->flow;

                $f->updated_at = date("Y-m-d H:i:s");
                $f->exists = true;
                $f->save();
            }
        }
    }

    /**
     * Removes all connections pointing to specified node id
     * 
     */
    private function deleteConnectionToNodeId($nodes)
    {
        foreach ($nodes as $kn => $n) {
            foreach ($n->connections as $kc => $c) {
                if (isset($c->if)) {
                    if (!$this->getNodeById($c->if->then, $nodes)) {//not found, delete connection
                        $nodes[$kn]->connections[$kc]->if->then = 'end';
                    }
                }
            }
        }
    }

    /**
     * Return node
     * 
     * @param int $id Node id
     * @param array $nodes Array of nodes
     * @return bool|array Node array or false if not found
     */    
    private function getNodeById($id, $nodes = null)
    {
        foreach ($nodes as $node) {
            if ($node->id == $id) {
                return $node;
            }
        }
        return false;
    }

}
