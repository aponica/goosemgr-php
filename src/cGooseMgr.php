<?php declare(strict_types=1);
//=============================================================================
// Copyright 2019-2022 Opplaud LLC and other contributors. MIT licensed.
//=============================================================================

namespace Aponica\GooseMgr;

//-----------------------------------------------------------------------------
///   Abstract interface to a relational or object database.
///
///   A Goose Manager provides a consistent way to access an object or
///   relational database.
///
///   It is not used directly, but serves as the base class for
///   <a href="https://aponica.com/docs/goosemgr-mysqlgoose-php/">
///     aponica/goosemgr-mysqlgoose-php</a> (to connect to a
///   <a href="https://www.mysql.com/">MySQL</a> database using
///   <a href="https://aponica.com/docs/mysqlgoose-php/">
///     aponica/mysqlgoose-php</a>.
//-----------------------------------------------------------------------------

abstract class cGooseMgr {

  private ?object $iConnection;
  private array $hModels = [];

  private ?array $hDefinitions;
  private ?object $iGoose;

  //---------------------------------------------------------------------------
  /// Private interface to connect to a database.
  ///
  /// Used by cGooseMgr::fConnect() to establish a connection.
  ///
  /// This method **must** be provided by a derived class!
  ///
  /// @param vConfig
  ///   The configuration information as expected by the Goose's method for
  ///   establishing a connection, typically containing such information as
  ///   the host, database, username and password.
  ///
  /// @returns Connection:
  ///   A connection to the database.
  //---------------------------------------------------------------------------

  abstract protected function fiCreateConnection( mixed $vConfig ) : object;


  //---------------------------------------------------------------------------
  /// Constructs a cGooseMgr object.
  ///
  /// This is invoked as parent::__construct() within a derived class.
  ///
  /// @param hDefinitions
  ///   The definitions hash (associative array) used to create schemas
  ///   for the database.
  ///
  ///   The hash contains one member for each table or collection to
  ///   be accessed. The name of the member is the name of the table or
  ///   collection, and its value is an object as expected by the `Schema`
  ///   class of the corresponding "Goose"
  ///   (<a href="https://aponica.com/docs/mysqlgoose-php/">Mysqlgoose</a>).
  ///
  ///   The hash may contain a property named `"//"` with any value;
  ///   it is assumed to be a comment member, and is ignored.
  ///
  /// @param iGoose
  ///   The "Goose" being managed.
  //---------------------------------------------------------------------------

  public function __construct( array $hDefinitions, object $iGoose ) {
    $this->hDefinitions = $hDefinitions;
    $this->iGoose = $iGoose;
    }


  //---------------------------------------------------------------------------
  /// Public interface to connect to a database.
  ///
  /// Establishes the connection to the database and creates the models
  /// used to access it.
  ///
  /// @param hConfig
  ///   The configuration information as expected by the Goose's method for
  ///   establishing a connection, typically containing such information as
  ///   the host, database, username and password.
  //---------------------------------------------------------------------------

  public function fConnect( mixed $vConfig ) : void {

    $this->iConnection = $this->fiCreateConnection( $vConfig );

    foreach ( $this->hDefinitions as $zModel => $hDef )
      if ( '//' !== $zModel )
        $this->hModels[ $zModel ] =
          $this->iConnection->model( $zModel, $this->iGoose->Schema($hDef) );

    } // fConnect()


  //---------------------------------------------------------------------------
  /// Retrieves the managed "Goose."
  ///
  /// @returns Object:
  ///   The "Goose" instance being managed by this GooseMgr.
  //---------------------------------------------------------------------------

  public function fiGoose() : object {
    return $this->iGoose;
    }


  //---------------------------------------------------------------------------
  /// Retrieves a model.
  ///
  /// @param zModel
  ///   The name of the desired model.
  ///
  /// @returns Object:
  ///   The "Model" instance providing the desired access. The actual type
  ///   will depend on the type of "Goose."
  //---------------------------------------------------------------------------

  public function fiModel( string $zName ) : object {
    return $this->hModels[ $zName ];
    }

  } // cGooseMgr

// EOF
