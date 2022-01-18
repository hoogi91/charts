.. include:: ../Includes.txt


.. _developer:

================
Developer Corner
================

Target group: **Developers**

This extension will support developers by providing

- registry to add multiple chart libraries implementing **Hoogi91\\Charts\\DataProcessing\\Charts\\LibraryInterface**

- DataProcessors to retrieve configured charts data, assets and flexform settings

.. hint::

   This extension can be combined with installation of `Spreadsheet extension <http://typo3.org/extensions/repository/view/spreadsheets/>`_


.. _register_library:

Register Library
================

To register your own chart library just implement **Hoogi91\\Charts\\DataProcessing\\Charts\\LibraryInterface**
and make sure it is loaded by your **Services.yaml**

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
