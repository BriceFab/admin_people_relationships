<?php


namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

class ExportUsersService
{
    private $em;
    private $exportCSVService;

    public const fileExportCase = "export_users";
    private $headersExportCase = ["email", "username", "roles", "actif", "derniereConnexion"];
    public const exportDirname = "export_user";
    public const exportFileName = "export_user.csv";
    private $emptyValue = "NA";
    private $dateFormat = "Y-m-d";

    public function __construct(EntityManagerInterface $em, ExportCSVService $exportCSVService)
    {
        $this->em = $em;
        $this->exportCSVService = $exportCSVService;
    }

    public function exportAllUser(): string
    {
        $this->exportCSVService->setConfig($this->headersExportCase, self::exportDirname, self::exportFileName, false, $this->emptyValue);

        /** @var UserRepository $userRepository */
        $userRepository = $this->em->getRepository(User::class);
        $qb = $userRepository->createQueryBuilder('entity');
        $qb->select('entity.email, entity.username, entity.roles, entity.enable, entity.derniereConnexion');

        foreach ($qb->getQuery()->getResult(Query::HYDRATE_ARRAY) as $i => $user) {
            $this->exportCSVService->addData('email', $user['email'], $i);
            $this->exportCSVService->addData('username', $user['username'], $i);
            $this->exportCSVService->addData('roles', $this->getFormattedRoles($user['roles']), $i);
            $this->exportCSVService->addData('enable', $user['enable'] ? "1" : "0", $i);
            $this->exportCSVService->addData('derniereConnexion', $this->getFormattedDate($user['derniereConnexion']), $i);
        }

        return $this->exportCSVService->generateCsv();
    }

    private function getFormattedDate($date): string
    {
        if ($date instanceof DateTime) {
            return $date->format($this->dateFormat);
        } else {
            return "";
        }
    }

    private function getFormattedRoles($roles): string
    {
        $s = "";
        foreach ($roles as $i => $role) {
            $s .= $role;
            if ($i != array_key_last($roles)) {
                $s .= ', ';
            }
        }
        return $s;
    }
}