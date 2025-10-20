using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;

namespace WpfRoosterMaker
{
    /// <summary>
    /// Interaction logic for MainWindow.xaml
    /// </summary>
    public partial class MainWindow : Window
    {
        public MainWindow()
        {
            InitializeComponent();
            LoadAgenda();
        }

        private void btnBackward_Click(object sender, RoutedEventArgs e)
        {
            ChangeDate(-1);
        }

        private void btnForward_Click(object sender, RoutedEventArgs e)
        {
            ChangeDate(1);
        }

        private void LoadAgenda()
        {
            dpWeek.SelectedDate = DateTime.Now;
        }

        private void ChangeDate(int index)
        {
            dpWeek.SelectedDate = ((DateTime)dpWeek.SelectedDate).AddDays(index * 7);
            for(int i = 0; i < 7; i++)
            {
                Console.WriteLine(dpWeek.SelectedDate);
            }
        }

        private void dpWeek_SelectedDateChanged(object sender, SelectionChangedEventArgs e)
        {
            var newDate = (DateTime)e.AddedItems[0];

            while (newDate.DayOfWeek != dpWeek.FirstDayOfWeek)
                newDate = newDate.AddDays(-1);

            dpWeek.SelectedDate = newDate;
        }

        private void btnCreate_Click(object sender, RoutedEventArgs e)
        {

        }
    }
}
