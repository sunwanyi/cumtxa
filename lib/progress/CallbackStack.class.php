<?PHP
/**
 * 调用堆栈控制类
 * @author linyh
 */
class CallbackStack implements Countable {
    protected $actionStack;
    function __construct() {
        $this->actionStack=array();
    }

    /**
     * 清空整个栈，将当前内容放到顶层
     * @param null|string $action
     */
    public function refresh($action=null){
        $this->actionStack=array();
        if($action){
            $this->actionStack[]=$action;
        }
    }

    /**
     * 增加一个动作
     * @param string $action
     */
    public function pushAction($action){
        $this->actionStack[]=$action;
    }

    /**
     * 获取最顶层的动作
     * @return string
     */
    public function getAction(){
        return end($this->actionStack);
    }

    /**
     * 弹出最顶层的动作
     * @return string
     */
    public function popAction(){
        return array_pop($this->actionStack);
    }

    /**
     * 把最顶层的动作替换为当前动作
     * @param string $action
     */
    public function updateAction($action){
        array_pop($this->actionStack);
        $this->actionStack[]=$action;
    }

    public function count() {
        return count($this->actionStack);
    }
}