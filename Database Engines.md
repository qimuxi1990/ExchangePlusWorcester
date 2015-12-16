# Database Engine
## Storage Engine
A storage engine is the part of a database that is responsible for managing how data is stored on disk. Many databases support multiple storage engines, where different engines perform better for specific workloads. For example, one storage engine might offer better performance for read-heavy workloads, and another might support a higher-throughput for write operations.
### MongoDB
> I can mix storage engines in a replica set, but it requires more maintaining.

|Engine|Features|
|---|---|
|MMAPv1|Default; based on memory mapping files; record all modifications to an on-disk *Journal*. good at high volumn inserts, reads, and in-place updates|
|Wired Tiger|New, 64-bit build; more powerful with concurrency control, snapshot/checkpoint, and compression|

#### MMAPv1 Storage Engine [@see](https://docs.mongodb.org/v3.0/core/mmapv1/)
MMAPv1 is MongoDB’s original storage engine based on memory mapped files. It excels at workloads with high volume inserts, reads, and in-place updates. MMAPv1 is the default storage engine in MongoDB 3.0 and all previous versions.

##### Durability and Recovery - Journal
In order to ensure that all modifications to a MongoDB data set are durably written to disk, MongoDB, by default, records all modifications to an on-disk journal. MongoDB writes more frequently to the journal than it writes the data files. The journal allows MongoDB to successfully recover data from data files after a mongod instance exits without flushing all changes.

##### Fragmentation and Allocation
When a document becomes larger than the allocated record, MongoDB must allocate a new record. New allocations require MongoDB to move a document and update all indexes that refer to the document, which takes more time than in-place updates and leads to storage fragmentation. 
*In v3.0, MongoDB uses Power of 2 Sized Allocations so that every document in MongoDB is stored in a record which contains the document itself and extra space, or padding. Padding allows the document to grow as the result of updates while minimizing the likelihood of reallocations.*
> you can disable the power of 2 allocation using the `collMod` command with the `noPadding` flag or the `db.createCollection()` method with the `noPadding` option.

|Strategy|Workload|Properties|
|---|---|---|
|Power of 2 Sized Allocations|insert/update/delete|reduce fragmentation and moves|
|No Padding Allocations|insert-only operations or update operations that do not increase document size|do not change the document sizes|

#### WiredTiger Storage Engine [@see](https://docs.mongodb.org/v3.0/core/wiredtiger/)
Starting in MongoDB 3.0, the WiredTiger storage engine is available in the 64-bit builds.

##### Concurrency Control
WiredTiger uses document-level concurrency control for write operations. As a result, multiple clients can modify different documents of a collection at the same time.

|Operations|Lock|
|---|---|
|most read and write operations|only intent locks at the global, database and collection levels|
|global operations, typically short lived operations involving multiple databases|still a global “instance-wide” lock|
|Some other operations, such as dropping a collection|still an exclusive database lock|

##### Durability and Recovery - Snapshot, Checkpoint and Journal

|Method|Description|
|---|---|
|Snapshot|At the start of an operation, presents a consistent view of the in-memory data|
|Checkpoint|Writing data Snapshot to disk acting as checkpoints in data files, ensure consistent up to and including the last checkpoint|
|Journal|write-ahead transaction log, to recover changes after last checkpoint|

##### Compression
With WiredTiger, MongoDB supports compression for all collections and indexes. Compression minimizes storage use at the expense of additional CPU.
Compression settings are also configurable on a per-collection and per-index basis during collection and index creation. See Specify Storage Engine Options and `db.collection.createIndex()` storageEngine option.
For most workloads, the default compression settings balance storage efficiency and processing requirements.
The WiredTiger journal is also compressed by default.

|Compressed Target|Default Compression|Alter Compression|Option|
|---|---|---|---|
|collections|block compression with the snappy|block compression with zlib is also available|`blockCompressor`|
|indexes|prefix compression|disable|`prefixCompression`|

Options: [@see](https://docs.mongodb.org/v3.0/reference/configuration-options/#storage-options)

```yaml
storage:
   dbPath: <string>
   indexBuildRetry: <boolean>
   repairPath: <string>
   journal:
      enabled: <boolean>
   directoryPerDB: <boolean>
   syncPeriodSecs: <int>
   engine: <string>
   mmapv1:
      preallocDataFiles: <boolean>
      nsSize: <int>
      quota:
         enforced: <boolean>
         maxFilesPerDB: <int>
      smallFiles: <boolean>
      journal:
         debugFlags: <int>
         commitIntervalMs: <num>
   wiredTiger:
      engineConfig:
         cacheSizeGB: <number>
         statisticsLogDelaySecs: <number>
         journalCompressor: <string>
         directoryForIndexes: <boolean>
      collectionConfig:
         blockCompressor: <string>
      indexConfig:
         prefixCompression: <boolean>
```