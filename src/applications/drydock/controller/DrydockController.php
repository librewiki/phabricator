<?php

abstract class DrydockController extends PhabricatorController {

  abstract public function buildSideNavView();

  public function buildApplicationMenu() {
    return $this->buildSideNavView()->getMenu();
  }

  protected function buildLocksTab($owner_phid) {
    $locks = DrydockSlotLock::loadLocks($owner_phid);

    $rows = array();
    foreach ($locks as $lock) {
      $rows[] = array(
        $lock->getID(),
        $lock->getLockKey(),
      );
    }

    $table = id(new AphrontTableView($rows))
      ->setNoDataString(pht('No slot locks held.'))
      ->setHeaders(
        array(
          pht('ID'),
          pht('Lock Key'),
        ))
      ->setColumnClasses(
        array(
          null,
          'wide',
        ));

    return id(new PHUIPropertyListView())
      ->addRawContent($table);
  }

  protected function buildCommandsTab($target_phid) {
    $viewer = $this->getViewer();

    $commands = id(new DrydockCommandQuery())
      ->setViewer($viewer)
      ->withTargetPHIDs(array($target_phid))
      ->execute();

    $consumed_yes = id(new PHUIIconView())
      ->setIconFont('fa-check green');
    $consumed_no = id(new PHUIIconView())
      ->setIconFont('fa-clock-o grey');

    $rows = array();
    foreach ($commands as $command) {
      $rows[] = array(
        $command->getID(),
        $viewer->renderHandle($command->getAuthorPHID()),
        $command->getCommand(),
        ($command->getIsConsumed()
          ? $consumed_yes
          : $consumed_no),
        phabricator_datetime($command->getDateCreated(), $viewer),
      );
    }

    $table = id(new AphrontTableView($rows))
      ->setNoDataString(pht('No commands issued.'))
      ->setHeaders(
        array(
          pht('ID'),
          pht('From'),
          pht('Command'),
          null,
          pht('Date'),
        ))
      ->setColumnClasses(
        array(
          null,
          null,
          'wide',
          null,
          null,
        ));

    return id(new PHUIPropertyListView())
      ->addRawContent($table);
  }

}
