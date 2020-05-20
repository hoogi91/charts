.. include:: ../Includes.txt


.. _developer:

================
Developer Corner
================

Target group: **Developers**

This extension will support developers by providing

- registry to add multiple chart libraries implementing at least the **Hoogi91\\Charts\\DataProcessing\\Charts\\LibraryInterface**

- DataProcessors to retrieve configured charts data, assets and flexform settings

.. hint::

   This extension can be combined with installation of `Spreadsheet extension <http://typo3.org/extensions/repository/view/spreadsheets/>`_

.. _new_library:

..
   Add new chart library
   ---------------------

   TODO!

.. _register_library:

Register Library
================

To register your own or an chart library override put something like the following in your `ext_localconf.php`:

.. code-block:: php

   /** @var \Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry $libraryRegistry */
    $libraryRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry::class
    );

   // add new chart library
   $libraryRegistry->register(
      'my-chart-library',
      \Vendor\MyExtKey\DataProcessing\Charts\Library\MyChartLibrary::class
   );

   // add override for existing chart library
   $libraryRegistry->register(
      'chart.js',
      \Vendor\MyExtKey\DataProcessing\Charts\Library\ChartJs::class,
      true
   );

.. _contribute:

Contribute
==========

Contributions are essential for the success of open-source projects but certainly not limited to contribute code. A lot more can be done:

- Improve documentation
- Answer questions on stackoverflow.com


Contribution workflow
---------------------

Please create always an issue at https://github.com/hoogi91/charts/issues before starting with a change. This is essential helpful if you are unsure if your change will be accepted.

Get the latest version from git
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Fork the repository https://github.com/hoogi91/charts and provide a pull request with your change
