<?php
    namespace App\Utils\MaterialContainer;
    use App\Utils\MaterialContainer\UserStoreMaterial;
    use App\Utils\MaterialContainer\TeamStoreMaterial;
    use App\Utils\MaterialContainer\ProjectStoreMaterial;
    use App\Utils\MaterialContainer\CommentStoreMaterial;
    use App\Utils\MaterialContainer\TaskStoreMaterial;
    class Material {

        use UserStoreMaterial;
        use TeamStoreMaterial;
        use ProjectStoreMaterial;
        use CommentStoreMaterial;
        use TaskStoreMaterial;
    }

?>