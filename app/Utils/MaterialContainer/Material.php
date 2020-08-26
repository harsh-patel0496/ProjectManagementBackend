<?php
    namespace App\Utils\MaterialContainer;
    use App\Utils\MaterialContainer\UserStoreMaterial;
    use App\Utils\MaterialContainer\TeamStoreMaterial;
    use App\Utils\MaterialContainer\ProjectStoreMaterial;
    class Material {

        use UserStoreMaterial;
        use TeamStoreMaterial;
        use ProjectStoreMaterial;
    }

?>