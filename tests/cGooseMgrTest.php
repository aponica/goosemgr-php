<?php declare(strict_types=1);
//=============================================================================
// Copyright 2019-2022 Opplaud LLC and other contributors. MIT licensed.
//=============================================================================

use Aponica\GooseMgr\cGooseMgr;
use PHPUnit\Framework\TestCase;

class cNoSchema {
  function  __construct( $hDef ) { $this->hDef = $hDef; }
  }

class cNoModel {
  public string $zModel;
  public cNoSchema $iSchema;
  function __construct( $zModel, $iSchema ) {
    $this->zModel = $zModel;
    $this->iSchema = $iSchema;
  } // __construct
} // cNoModel

class cNoConnection {
  function model( $zModel, $iSchema ) : object {
    return new cNoModel( $zModel, $iSchema );
    } // model
  } // cConnection

class cDerivedClass extends cGooseMgr {
  function fiCreateConnection( $hConfig ) : cNoConnection {
    return new cNoConnection(); }
  } // cDerivedClass

class cNoGoose {
  function Schema( $hDef ) : cNoSchema {
    return new cNoSchema( $hDef ); }
}

final class cGooseMgrTest extends TestCase {

  //---------------------------------------------------------------------------

  public function testDerivedClassMethods() {

    try {

      $hTable1 = [ 'foo' => 'bar' ];

      $hDefs = [ '//' => [], 'table1' => $hTable1 ];

      $iNoGoose = new cNoGoose();

      $iGooseMgr = new cDerivedClass( $hDefs, $iNoGoose );

      $iGooseMgr->fConnect( [] );

      $this->assertInstanceOf( cNoGoose::class, $iGooseMgr->fiGoose() );

      $iModel = $iGooseMgr->fiModel( 'table1' );

      $this->assertSame( 'table1', $iModel->zModel );

      $this->assertSame( $hTable1, $iModel->iSchema->hDef );

      $this->expectException(
        \PHPUnit\Framework\ExpectationFailedException::class );

      $this->expectExceptionMessage( 'Undefined array key "//"' );

      $iGooseMgr->fiModel( '//' );

      }
    catch ( Throwable $iThrown ) {
      $this->assertSame( 'to never happen', $iThrown );
      }

    } // testDerivedClassMethods

} // cGooseMgrTest

// EOF
