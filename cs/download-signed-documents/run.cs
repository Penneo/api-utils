using System;
using Penneo;
using System.Collections.Generic;
using System.Linq;
using System.IO;
using System.Diagnostics;

namespace Penneo
{
    public class DownloadSignedDocuments
    {
        public static void Main(string[] args)
        {
            if (args.Length != 4) {
                Console.WriteLine("Parameters required: endpoint, key, secret, case-file-id");
                Environment.Exit(-1);
            }

            string endpoint = args[0];
            string key      = args[1];
            string secret   = args[2];

            int caseFileId  = System.Convert.ToInt32(args[3]);

            PenneoConnector.Initialize(key, secret, endpoint);
            PenneoConnector.SetLogger(new Logger());
            run(caseFileId);
        }

        public static void run(int caseFileId)
        {
            var caseFile = Query.Find<CaseFile>(caseFileId);

            // Check empty response
            if (caseFile == null) {
                Console.WriteLine("Nothing found");
                return;
            }

            Console.WriteLine("Case File Id: " + caseFile.Id);

            var documents = caseFile.GetDocuments();

            foreach (Document document in documents)
            {
                Console.WriteLine("- " + document.Id + " : " + document.Title + "(" + document.GetStatus() + ")");
                if (document.GetStatus().Equals("completed")) {
                    File.WriteAllBytes("downloaded-" + document.Id + ".pdf", document.GetPdf());
                }
            }
        }
    }

    internal class Logger : ILogger
    {
        public void Log(string message, LogSeverity severity)
        {
            if (severity >= LogSeverity.Error)
            {
                Debug.WriteLine(severity + ": " + message);
            }
        }
    }
}
